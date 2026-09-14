import Swiper from 'swiper';
import {
  A11y,
  Autoplay,
  EffectFade,
  Navigation,
  Pagination
} from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';

const SWIPER_SELECTOR = '[data-apache-swiper]';

function getControlledElement(el, selector) {
  if (!el.id) {
    return null;
  }

  const matches = document.querySelectorAll(selector);

  for (const match of matches) {
    if (match.getAttribute('aria-controls') === el.id) {
      return match;
    }
  }

  return null;
}

function getNavigationOptions(el) {
  const nextEl = el.querySelector('[data-swiper-next]')
    || el.querySelector('[data-hero-next]')
    || getControlledElement(el, '[data-swiper-next]')
    || el.querySelector('.swiper-button-next');
  const prevEl = el.querySelector('[data-swiper-prev]')
    || el.querySelector('[data-hero-prev]')
    || getControlledElement(el, '[data-swiper-prev]')
    || el.querySelector('.swiper-button-prev');

  if (!nextEl || !prevEl) {
    return false;
  }

  return {
    nextEl,
    prevEl
  };
}

function getPaginationOptions(el) {
  const paginationEl = el.querySelector('.swiper-pagination');

  if (!paginationEl) {
    return false;
  }

  return {
    el: paginationEl,
    clickable: true
  };
}

function parseBooleanDataValue(value, fallback) {
  if (typeof value !== 'string' || value === '') {
    return fallback;
  }

  return !['0', 'false', 'no', 'off'].includes(value.trim().toLowerCase());
}

function getSharedSliderOptions(el, defaults = {}) {
  const effect = typeof el.dataset.swiperEffect === 'string' && el.dataset.swiperEffect !== ''
    ? el.dataset.swiperEffect
    : (defaults.effect || 'slide');
  const loop = parseBooleanDataValue(el.dataset.swiperLoop, defaults.loop ?? true);
  const autoplayEnabled = parseBooleanDataValue(el.dataset.swiperAutoplay, defaults.autoplay ?? false);
  const autoplayDelay = Number.parseInt(el.dataset.swiperAutoplayDelay || '', 10);
  const options = {
    loop
  };

  if (autoplayEnabled) {
    options.autoplay = {
      delay: Number.isFinite(autoplayDelay) ? autoplayDelay : 5000,
      disableOnInteraction: false,
      pauseOnMouseEnter: true
    };
  } else {
    options.autoplay = false;
  }

  if (effect === 'fade') {
    options.effect = 'fade';
    options.fadeEffect = {
      crossFade: true
    };
  }

  return options;
}

function getVariantOptions(el) {
  const variant = el.dataset.apacheSwiper || 'default';
  const slideCount = el.querySelectorAll('.swiper-slide').length;

  if (variant === 'hero') {
    return {
      speed: 500,
      keyboard: {
        enabled: true,
        onlyInViewport: true
      },
      ...getSharedSliderOptions(el, {
        loop: slideCount > 1,
        autoplay: slideCount > 1,
        effect: 'slide'
      })
    };
  }

  if (variant === 'tabs') {
    const pagination = getPaginationOptions(el);

    if (!pagination) {
      return {};
    }

    return {
      pagination: {
        ...pagination,
        renderBullet(index, className) {
          const slide = el.querySelectorAll('.swiper-slide')[index];
          const label = slide ? slide.getAttribute('title') || String(index + 1) : String(index + 1);

          return `<button class="${className}" type="button">${label}</button>`;
        }
      }
    };
  }

  if (variant === 'media') {
    return {
      autoHeight: true,
      speed: 600,
      on: {
        slideChangeTransitionStart(swiper) {
          pauseSlideVideos(swiper);
        },
        slideChangeTransitionEnd(swiper) {
          playActiveVideo(swiper);
        },
        imagesReady() {
          el.querySelectorAll('.slide-image').forEach((image) => {
            image.classList.add('show');
          });
        }
      },
      ...getSharedSliderOptions(el, {
        loop: true,
        autoplay: true,
        effect: 'slide'
      }),
      autoplay: parseBooleanDataValue(el.dataset.swiperAutoplay, true)
        ? {
            delay: Number.parseInt(el.dataset.swiperAutoplayDelay || '', 10) || 2500,
            disableOnInteraction: false,
            pauseOnMouseEnter: true
          }
        : false
    };
  }

  if (variant === 'featured-posts') {
    return {
      slidesPerView: 1,
      slidesPerGroup: 1,
      spaceBetween: 20,
      breakpoints: {
        768: {
          slidesPerView: 2,
          spaceBetween: 24
        },
        1024: {
          slidesPerView: 3,
          spaceBetween: 24
        }
      },
      ...getSharedSliderOptions(el, {
        loop: slideCount > 1,
        autoplay: false,
        effect: 'slide'
      })
    };
  }

  if (variant === 'gallery') {
    const desktopColumns = Number.parseInt(el.dataset.galleryColumns || '', 10);
    const resolvedColumns = Number.isFinite(desktopColumns) && desktopColumns >= 2 && desktopColumns <= 4
      ? desktopColumns
      : 3;

    return {
      slidesPerView: 1,
      slidesPerGroup: 1,
      spaceBetween: 20,
      breakpoints: {
        768: {
          slidesPerView: Math.min(resolvedColumns, 2),
          spaceBetween: 24
        },
        1024: {
          slidesPerView: Math.min(resolvedColumns, 3),
          spaceBetween: 24
        },
        1280: {
          slidesPerView: resolvedColumns,
          spaceBetween: 24
        }
      },
      ...getSharedSliderOptions(el, {
        loop: slideCount > 1,
        autoplay: false,
        effect: 'slide'
      })
    };
  }

  if (variant === 'fade') {
    return getSharedSliderOptions(el, {
      loop: true,
      autoplay: false,
      effect: 'fade'
    });
  }

  return getSharedSliderOptions(el, {
    loop: true,
    autoplay: false,
    effect: 'slide'
  });
}

function pauseSlideVideos(swiper) {
  swiper.slides.forEach((slide) => {
    slide.querySelectorAll('video').forEach((video) => video.pause());
  });
}

function playActiveVideo(swiper) {
  const activeSlide = swiper.slides[swiper.activeIndex];

  if (!activeSlide) {
    return;
  }

  activeSlide.querySelectorAll('video').forEach((video) => video.play());
}

export function initApacheSwipers(context = document) {
  const sliders = context.querySelectorAll(SWIPER_SELECTOR);

  sliders.forEach((el) => {
    if (el.swiper) {
      return;
    }

    const slideCount = el.querySelectorAll('.swiper-slide').length;
    const variant = el.dataset.apacheSwiper || 'default';

    if (variant === 'hero' && slideCount < 2) {
      el.classList.add('is-static');
      return;
    }

    const navigation = getNavigationOptions(el);
    const pagination = getPaginationOptions(el);

    new Swiper(el, {
      modules: [Navigation, Pagination, Autoplay, EffectFade, A11y],
      slidesPerView: 1,
      slidesPerGroup: 1,
      spaceBetween: 24,
      speed: 500,
      watchOverflow: true,
      navigation,
      pagination,
      a11y: true,
      ...getVariantOptions(el)
    });
  });
}
