const POPUP_OPEN_CLASS = 'is-open';
const BODY_OPEN_CLASS = 'has-popup-open';
const FOCUSABLE_SELECTOR = [
  'a[href]',
  'area[href]',
  'button:not([disabled])',
  'input:not([disabled]):not([type="hidden"])',
  'select:not([disabled])',
  'textarea:not([disabled])',
  'iframe',
  'object',
  'embed',
  '[contenteditable]',
  '[tabindex]:not([tabindex="-1"])',
].join(', ');

let activePopup = null;

function prefersReducedMotion() {
  return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

function getTransitionDuration() {
  return prefersReducedMotion() ? 0 : 350;
}

function getFocusableElements(container) {
  return Array.from(container.querySelectorAll(FOCUSABLE_SELECTOR)).filter((element) => {
    if (!(element instanceof HTMLElement)) {
      return false;
    }

    if (element.hasAttribute('hidden') || element.getAttribute('aria-hidden') === 'true') {
      return false;
    }

    return element.offsetParent !== null || element === document.activeElement;
  });
}

function lockPageScroll() {
  const scrollbarWidth = Math.max(0, window.innerWidth - document.documentElement.clientWidth);

  document.documentElement.classList.add(BODY_OPEN_CLASS);
  document.body.classList.add(BODY_OPEN_CLASS);

  if (scrollbarWidth > 0) {
    document.body.style.setProperty('--apache-popup-scrollbar-offset', `${scrollbarWidth}px`);
    document.body.style.paddingRight = `${scrollbarWidth}px`;
  }
}

function unlockPageScroll() {
  document.documentElement.classList.remove(BODY_OPEN_CLASS);
  document.body.classList.remove(BODY_OPEN_CLASS);
  document.body.style.removeProperty('--apache-popup-scrollbar-offset');
  document.body.style.paddingRight = '';
}

function focusPopup(popup) {
  const dialog = popup.querySelector('[data-popup-dialog]');

  if (!(dialog instanceof HTMLElement)) {
    return;
  }

  const focusTarget = getFocusableElements(dialog)[0] || dialog;
  focusTarget.focus();
}

function stopPopupMedia(popup) {
  if (!(popup instanceof HTMLElement)) {
    return;
  }

  popup.querySelectorAll('[data-apache-video]').forEach((videoRoot) => {
    if (videoRoot instanceof HTMLElement) {
      videoRoot.dispatchEvent(new CustomEvent('apache:video-stop', { bubbles: true }));
    }
  });

  popup.querySelectorAll('video').forEach((video) => {
    if (video instanceof HTMLMediaElement) {
      video.pause();
    }
  });

  popup.querySelectorAll('iframe').forEach((iframe) => {
    if (!(iframe instanceof HTMLIFrameElement) || !iframe.contentWindow) {
      return;
    }

    const src = iframe.src || '';

    if (src.includes('youtube.com') || src.includes('youtube-nocookie.com')) {
      iframe.contentWindow.postMessage(
        JSON.stringify({
          event: 'command',
          func: 'pauseVideo',
          args: [],
        }),
        '*'
      );
    }

    if (src.includes('player.vimeo.com')) {
      iframe.contentWindow.postMessage(
        JSON.stringify({
          method: 'pause',
        }),
        '*'
      );
    }
  });
}

function openPopup(popup, trigger = null) {
  if (!(popup instanceof HTMLElement)) {
    return;
  }

  if (activePopup && activePopup !== popup) {
    closePopup(activePopup, { restoreFocus: false });
  }

  popup._popupReturnFocusEl = trigger instanceof HTMLElement ? trigger : document.activeElement;

  popup.hidden = false;
  popup.setAttribute('aria-hidden', 'false');
  popup.classList.add('is-rendered');
  lockPageScroll();

  window.requestAnimationFrame(() => {
    popup.classList.add(POPUP_OPEN_CLASS);
    focusPopup(popup);
  });

  activePopup = popup;
}

function closePopup(popup, { restoreFocus = true } = {}) {
  if (!(popup instanceof HTMLElement)) {
    return;
  }

  stopPopupMedia(popup);
  popup.classList.remove(POPUP_OPEN_CLASS);
  popup.setAttribute('aria-hidden', 'true');

  const finishClose = () => {
    popup.hidden = true;
    popup.classList.remove('is-rendered');

    if (restoreFocus && popup._popupReturnFocusEl instanceof HTMLElement) {
      popup._popupReturnFocusEl.focus();
    }

    if (activePopup === popup) {
      activePopup = null;
      unlockPageScroll();
    }
  };

  if (popup._popupCloseTimer) {
    window.clearTimeout(popup._popupCloseTimer);
  }

  popup._popupCloseTimer = window.setTimeout(finishClose, getTransitionDuration());
}

function bindPopup(popup) {
  if (!(popup instanceof HTMLElement) || popup.dataset.popupReady === 'true') {
    return;
  }

  popup.dataset.popupReady = 'true';

  popup.addEventListener('click', (event) => {
    const target = event.target;

    if (!(target instanceof HTMLElement)) {
      return;
    }

    if (target.closest('[data-popup-close]')) {
      event.preventDefault();
      closePopup(popup);
    }
  });
}

function bindTrigger(popup, trigger) {
  if (!(popup instanceof HTMLElement) || !(trigger instanceof HTMLElement)) {
    return;
  }

  const popupId = popup.dataset.popupId || '';
  const readyKey = `popupTriggerReady${popupId}`;

  if (trigger.dataset[readyKey] === 'true') {
    return;
  }

  trigger.dataset[readyKey] = 'true';

  trigger.addEventListener('click', (event) => {
    event.preventDefault();
    openPopup(popup, trigger);
  });
}

function bindTriggers(popup) {
  const triggerClass = (popup.dataset.popupTriggerClass || '').trim();

  if (!triggerClass) {
    return;
  }

  const safeSelector = window.CSS && typeof window.CSS.escape === 'function'
    ? `.${window.CSS.escape(triggerClass)}`
    : `.${triggerClass.replace(/[^A-Za-z0-9_-]/g, '\\$&')}`;

  document.querySelectorAll(safeSelector).forEach((trigger) => {
    bindTrigger(popup, trigger);
  });
}

function handleKeydown(event) {
  if (!(activePopup instanceof HTMLElement)) {
    return;
  }

  if (event.key === 'Escape') {
    event.preventDefault();
    closePopup(activePopup);
    return;
  }

  if (event.key !== 'Tab') {
    return;
  }

  const dialog = activePopup.querySelector('[data-popup-dialog]');

  if (!(dialog instanceof HTMLElement)) {
    return;
  }

  const focusable = getFocusableElements(dialog);

  if (focusable.length === 0) {
    event.preventDefault();
    dialog.focus();
    return;
  }

  const first = focusable[0];
  const last = focusable[focusable.length - 1];

  if (event.shiftKey && document.activeElement === first) {
    event.preventDefault();
    last.focus();
  } else if (!event.shiftKey && document.activeElement === last) {
    event.preventDefault();
    first.focus();
  }
}

function scheduleAutoOpen(popups) {
  const autoPopup = popups.find((popup) => popup.dataset.popupAutoOpen === 'true');

  if (!(autoPopup instanceof HTMLElement)) {
    return;
  }

  const delay = Number.parseInt(autoPopup.dataset.popupAutoOpenDelay || '0', 10);

  window.setTimeout(() => {
    if (!activePopup) {
      openPopup(autoPopup);
    }
  }, Number.isFinite(delay) && delay > 0 ? delay : 0);
}

export function initPopups(context = document) {
  const popups = Array.from(context.querySelectorAll('[data-popup]')).filter((popup) => popup instanceof HTMLElement);

  if (popups.length === 0) {
    return;
  }

  popups.forEach((popup) => {
    bindPopup(popup);
    bindTriggers(popup);
  });

  if (document.documentElement.dataset.apachePopupKeydownReady !== 'true') {
    document.documentElement.dataset.apachePopupKeydownReady = 'true';
    document.addEventListener('keydown', handleKeydown);
  }

  scheduleAutoOpen(popups);
}
