function getJQuery() {
  return window.jQuery || window.$ || null;
}

export function initBootstrapBehaviors(context = document) {
  const $ = getJQuery();

  if (!$) {
    return;
  }

  const $context = $(context);

  addBrowserClasses($);
  initStickyNavbar($);

  $context.find('.announcement-banner').addBack('.announcement-banner').off('click.apache2026').on('click.apache2026', function () {
    if ($.fn.alert) {
      $(this).find('.alert').addBack('.alert').alert('close');
    }

    $('.navbar-expand-lg').css('top', '0px');
    $('.admin-bar .navbar-expand-lg').css('top', '31px');
  });

  $context.find('.employee[data-toggle="collapse"], .employee[data-bs-toggle="collapse"]').off('click.apache2026').on('click.apache2026', function () {
    const $button = $(this);
    const isActive = !$button.hasClass('active');

    $button.toggleClass('active opened', isActive);
    $button.text(isActive ? 'Close Full Bio' : 'Expand Full Bio');
  });

  if (window.location.hash) {
    let target = null;

    try {
      target = context.querySelector(window.location.hash);
    } catch (error) {
      target = null;
    }

    if (target && !target.dataset.apache2026HashOpened) {
      target.dataset.apache2026HashOpened = 'true';
      $(target).find('.employee-info').addClass('collapse show');
      $(target).find('.employee').text('Close Full Bio').addClass('active opened');

      setTimeout(() => {
        $('html, body').animate({
          scrollTop: $(target).offset().top - 240
        }, 500);
      }, 500);
    }
  }
}

function addBrowserClasses($) {
  const userAgent = navigator.userAgent.toLowerCase();
  const $html = $('html');

  if (/(iphone|ipod|ipad)/.test(userAgent)) {
    $html.addClass('ios mobile');
  }

  if (navigator.userAgent.includes('MSIE')) {
    $html.addClass('ie');
  } else if (navigator.userAgent.includes('Chrome')) {
    $html.addClass('chrome');
  } else if (navigator.userAgent.includes('Firefox')) {
    $html.addClass('firefox');
  } else if (navigator.userAgent.includes('Safari') && !navigator.userAgent.includes('Chrome')) {
    $html.addClass('safari');
  } else if (navigator.userAgent.includes('Opera')) {
    $html.addClass('opera');
  }
}

function initStickyNavbar($) {
  const $window = $(window);
  const $body = $('body');
  const $navbar = $('nav.navbar');

  if (!$navbar.length || $navbar.data('apache2026StickyReady')) {
    return;
  }

  $navbar.data('apache2026StickyReady', true);

  const $logo = $navbar.find('.navbar-brand');
  const $navbarToggler = $navbar.find('.navbar-toggler');
  const $announcementBanner = $('.announcement-banner');
  const maxScroll = 62;
  const wpAdminBarHeight = $('#wpadminbar').outerHeight() || 0;

  function render() {
    const bannerHeight = $announcementBanner.length ? $announcementBanner.outerHeight() : 0;
    const mobileNav = $navbarToggler.is(':visible');
    const maxScrollWithBanner = mobileNav ? bannerHeight : maxScroll + bannerHeight;
    const scrollRange = maxScrollWithBanner === 0 ? 1 : Math.min(maxScrollWithBanner, $window.scrollTop()) / maxScrollWithBanner;

    $body.css('margin-top', bannerHeight);
    $navbar.css(
      scrollRange >= 1
        ? { position: 'fixed', top: mobileNav ? 0 : `-${maxScroll - wpAdminBarHeight}px` }
        : { position: 'absolute', top: `${bannerHeight + wpAdminBarHeight}px` }
    );

    $logo.css(
      'transform',
      mobileNav ? '' : `scale(${1 - scrollRange * 0.35}) translateY(${scrollRange * 30}px)`
    );
  }

  $window.on('scroll.apache2026 resize.apache2026', render);
  render();
}

window.apache2026InitBootstrap = initBootstrapBehaviors;
