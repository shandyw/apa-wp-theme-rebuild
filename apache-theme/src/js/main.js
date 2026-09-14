import { initSiteHeader } from './header.js';
import { initBrowserClasses } from './modules/browser-classes.js';

async function loadAndInitWhenPresent(selector, importer, exportName, context = document) {
  if (!context.querySelector(selector)) {
    return null;
  }

  const module = await importer();
  const init = module?.[exportName];

  if (typeof init === 'function') {
    init(context);
  }

  return init ?? null;
}

document.addEventListener('DOMContentLoaded', () => {
  document.documentElement.classList.add('js');
  initBrowserClasses(document.documentElement);
  initSiteHeader(document);

  void Promise.all([
    loadAndInitWhenPresent(
      '.accordion-wrapper, [data-accordion-toggle], .accordion-trigger',
      () => import('./modules/accordion.js'),
      'initAccordions',
    ),
    loadAndInitWhenPresent(
      '[data-apache-swiper]',
      () => import('./modules/swiper.js'),
      'initApacheSwipers',
    ),
    loadAndInitWhenPresent(
      '[data-gallery-lightbox], [data-gallery-lightbox-trigger]',
      () => import('./modules/gallery.js'),
      'initGalleryLightboxes',
    ),
    loadAndInitWhenPresent(
      '[data-apache-video]',
      () => import('./modules/video.js'),
      'initVideoPlayers',
    ),
    loadAndInitWhenPresent(
      '.hover-boxes__card',
      () => import('./modules/hover-boxes.js'),
      'initHoverBoxes',
    ),
    loadAndInitWhenPresent(
      '[data-home-map]',
      () => import('./modules/d3.js'),
      'initMaps',
    ),
    loadAndInitWhenPresent(
      '[data-contact-departments]',
      () => import('./modules/contact-information.js'),
      'initContactInformation',
    ),
  ]);
});
