function getLightboxTriggers(dialog) {
  if (!(dialog instanceof HTMLDialogElement) || !dialog.id) {
    return [];
  }

  return Array.from(
    document.querySelectorAll(`[data-gallery-lightbox-trigger][data-gallery-lightbox-target="${dialog.id}"]`)
  ).filter((trigger) => trigger instanceof HTMLButtonElement);
}

function getAdjacentTrigger(dialog, currentTrigger, direction) {
  const triggers = getLightboxTriggers(dialog);

  if (triggers.length < 2) {
    return null;
  }

  const currentIndex = triggers.indexOf(currentTrigger);

  if (currentIndex === -1) {
    return direction > 0 ? triggers[0] : triggers[triggers.length - 1];
  }

  const nextIndex = (currentIndex + direction + triggers.length) % triggers.length;

  return triggers[nextIndex] || null;
}

function populateLightbox(dialog, trigger) {
  const image = dialog.querySelector('[data-gallery-lightbox-image]');
  const caption = dialog.querySelector('[data-gallery-lightbox-caption]');
  const meta = dialog.querySelector('[data-gallery-lightbox-meta]');
  const downloads = dialog.querySelector('[data-gallery-lightbox-downloads]');
  const lowResLink = dialog.querySelector('[data-gallery-lightbox-low-res]');
  const highResLink = dialog.querySelector('[data-gallery-lightbox-high-res]');
  const separator = dialog.querySelector('[data-gallery-lightbox-separator]');

  if (!(image instanceof HTMLImageElement)) {
    return;
  }

  image.src = trigger.dataset.galleryLightboxSrc || '';
  image.alt = trigger.dataset.galleryLightboxAlt || '';

  if (caption && meta) {
    const captionText = (trigger.dataset.galleryLightboxCaption || '').trim();
    caption.textContent = captionText;

    const lowResUrl = (trigger.dataset.galleryLightboxLowResUrl || '').trim();
    const highResUrl = (trigger.dataset.galleryLightboxHighResUrl || '').trim();
    const hasLowRes = lowResUrl !== '';
    const hasHighRes = highResUrl !== '';
    const hasDownloads = hasLowRes || hasHighRes;

    if (lowResLink instanceof HTMLAnchorElement) {
      lowResLink.href = hasLowRes ? lowResUrl : '';
      lowResLink.hidden = !hasLowRes;

      if (trigger.dataset.galleryLightboxLowResDownload === 'true') {
        lowResLink.setAttribute('download', '');
      } else {
        lowResLink.removeAttribute('download');
      }
    }

    if (highResLink instanceof HTMLAnchorElement) {
      highResLink.href = hasHighRes ? highResUrl : '';
      highResLink.hidden = !hasHighRes;

      if (trigger.dataset.galleryLightboxHighResDownload === 'true') {
        highResLink.setAttribute('download', '');
      } else {
        highResLink.removeAttribute('download');
      }
    }

    if (separator) {
      separator.hidden = !(hasLowRes && hasHighRes);
    }

    if (downloads) {
      downloads.hidden = !hasDownloads;
    }

    meta.hidden = captionText === '' && !hasDownloads;
  }

  dialog.dataset.galleryLightboxReturnFocus = trigger.dataset.galleryLightboxLabel || '';
  dialog._returnFocusEl = trigger;
}

function openLightbox(dialog, trigger) {
  populateLightbox(dialog, trigger);

  if (typeof dialog.showModal === 'function') {
    if (!dialog.open) {
      dialog.showModal();
    }
  } else {
    dialog.setAttribute('open', 'open');
  }
}

function closeLightbox(dialog) {
  const image = dialog.querySelector('[data-gallery-lightbox-image]');
  const caption = dialog.querySelector('[data-gallery-lightbox-caption]');
  const meta = dialog.querySelector('[data-gallery-lightbox-meta]');
  const downloads = dialog.querySelector('[data-gallery-lightbox-downloads]');
  const lowResLink = dialog.querySelector('[data-gallery-lightbox-low-res]');
  const highResLink = dialog.querySelector('[data-gallery-lightbox-high-res]');
  const separator = dialog.querySelector('[data-gallery-lightbox-separator]');

  if (typeof dialog.close === 'function') {
    dialog.close();
  } else {
    dialog.removeAttribute('open');
  }

  if (image instanceof HTMLImageElement) {
    image.src = '';
    image.alt = '';
  }

  if (caption) {
    caption.textContent = '';
  }

  if (lowResLink instanceof HTMLAnchorElement) {
    lowResLink.href = '';
    lowResLink.hidden = true;
    lowResLink.removeAttribute('download');
  }

  if (highResLink instanceof HTMLAnchorElement) {
    highResLink.href = '';
    highResLink.hidden = true;
    highResLink.removeAttribute('download');
  }

  if (separator) {
    separator.hidden = true;
  }

  if (downloads) {
    downloads.hidden = true;
  }

  if (meta) {
    meta.hidden = true;
  }

  if (dialog._returnFocusEl instanceof HTMLElement) {
    dialog._returnFocusEl.focus();
  }
}

export function initGalleryLightboxes(context = document) {
  const dialogs = context.querySelectorAll('[data-gallery-lightbox]');

  dialogs.forEach((dialog) => {
    if (!(dialog instanceof HTMLDialogElement) || dialog.dataset.galleryLightboxReady === 'true') {
      return;
    }

    dialog.dataset.galleryLightboxReady = 'true';

    dialog.addEventListener('click', (event) => {
      const target = event.target;

      if (!(target instanceof HTMLElement)) {
        return;
      }

      if (target === dialog || target.closest('[data-gallery-lightbox-close]')) {
        closeLightbox(dialog);
      }
    });

    dialog.addEventListener('cancel', (event) => {
      event.preventDefault();
      closeLightbox(dialog);
    });

    dialog.addEventListener('keydown', (event) => {
      if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') {
        return;
      }

      const currentTrigger = dialog._returnFocusEl;

      if (!(currentTrigger instanceof HTMLButtonElement)) {
        return;
      }

      const nextTrigger = getAdjacentTrigger(dialog, currentTrigger, event.key === 'ArrowRight' ? 1 : -1);

      if (!(nextTrigger instanceof HTMLButtonElement)) {
        return;
      }

      event.preventDefault();
      populateLightbox(dialog, nextTrigger);
    });
  });

  const triggers = context.querySelectorAll('[data-gallery-lightbox-trigger]');

  triggers.forEach((trigger) => {
    if (!(trigger instanceof HTMLButtonElement) || trigger.dataset.galleryLightboxReady === 'true') {
      return;
    }

    trigger.dataset.galleryLightboxReady = 'true';

    trigger.addEventListener('click', () => {
      const dialogId = trigger.dataset.galleryLightboxTarget || '';
      const dialog = dialogId ? document.getElementById(dialogId) : null;

      if (!(dialog instanceof HTMLDialogElement)) {
        return;
      }

      openLightbox(dialog, trigger);
    });
  });
}
