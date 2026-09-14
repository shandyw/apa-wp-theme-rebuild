export function initSiteHeader(context = document) {
  const root = context.querySelector('[data-site-header]');

  if (!root || root.dataset.headerReady === 'true') {
    return;
  }

  const toggle = root.querySelector('[data-mobile-menu-toggle]');
  const panel = root.querySelector('[data-mobile-menu]');
  const announcementClose = context.querySelector('[data-announcement-close]');
  const announcementBanner = context.querySelector('[data-announcement-banner]');
  const expandableSearches = root.querySelectorAll('[data-expandable-search]');
  const primaryNav = root.querySelector('[data-primary-nav]');
  const desktopQuery = window.matchMedia('(min-width: 992px)');
  const stickyThreshold = 80;
  const stickyReleaseThreshold = 8;
  let stickyTicking = false;
  let isSticky = false;

  if (!toggle || !panel) {
    return;
  }

  root.dataset.headerReady = 'true';

  const setStickyState = () => {
    const shouldStick = desktopQuery.matches
      ? (isSticky ? window.scrollY > stickyReleaseThreshold : window.scrollY > stickyThreshold)
      : false;

    if (shouldStick === isSticky) {
      return;
    }

    root.classList.toggle('site-header--sticky', shouldStick);
    document.documentElement.classList.toggle('has-sticky-site-header', shouldStick);
    isSticky = shouldStick;
  };

  const requestStickyUpdate = () => {
    if (stickyTicking) {
      return;
    }

    stickyTicking = true;

    window.requestAnimationFrame(() => {
      setStickyState();
      stickyTicking = false;
    });
  };

  setStickyState();
  window.addEventListener('scroll', requestStickyUpdate, { passive: true });
  window.addEventListener('resize', requestStickyUpdate);

  if (typeof desktopQuery.addEventListener === 'function') {
    desktopQuery.addEventListener('change', setStickyState);
  } else if (typeof desktopQuery.addListener === 'function') {
    desktopQuery.addListener(setStickyState);
  }

  if (primaryNav) {
    const megaItems = primaryNav.querySelectorAll('.primary-nav__item--has-children');
    const closeTimers = new WeakMap();
    let megaWrapTicking = false;

    const updateMegaWrapState = (item) => {
      const inner = item.querySelector('.primary-nav__mega-inner');

      if (!inner) {
        return;
      }

      const columns = inner.querySelectorAll('.primary-nav__mega-column');
      const styles = window.getComputedStyle(inner);
      const columnGap = Number.parseFloat(styles.columnGap) || 0;
      const minColumnWidth = 160;
      const requiredWidth = (columns.length * minColumnWidth) + (Math.max(0, columns.length - 1) * columnGap);

      inner.classList.toggle('primary-nav__mega-inner--wrapped', requiredWidth > inner.clientWidth);
    };

    const updateAllMegaWrapStates = () => {
      megaItems.forEach(updateMegaWrapState);
    };

    const requestMegaWrapUpdate = () => {
      if (megaWrapTicking) {
        return;
      }

      megaWrapTicking = true;

      window.requestAnimationFrame(() => {
        updateAllMegaWrapStates();
        megaWrapTicking = false;
      });
    };

    const clearCloseTimer = (item) => {
      const timer = closeTimers.get(item);

      if (timer) {
        window.clearTimeout(timer);
        closeTimers.delete(item);
      }
    };

    const scheduleCloseMegaItem = (item) => {
      clearCloseTimer(item);

      closeTimers.set(
        item,
        window.setTimeout(() => {
          closeMegaItem(item);
          closeTimers.delete(item);
        }, 180),
      );
    };

    const closeMegaItem = (item) => {
      const trigger = item.querySelector('.primary-nav__trigger');

      clearCloseTimer(item);
      item.classList.remove('primary-nav__item--open');
      trigger?.setAttribute('aria-expanded', 'false');
    };

    const closeAllMegaItems = (exceptItem = null) => {
      megaItems.forEach((item) => {
        if (item !== exceptItem) {
          closeMegaItem(item);
        }
      });
    };

    const openMegaItem = (item) => {
      const trigger = item.querySelector('.primary-nav__trigger');

      clearCloseTimer(item);
      closeAllMegaItems(item);
      updateMegaWrapState(item);
      item.classList.add('primary-nav__item--open');
      trigger?.setAttribute('aria-expanded', 'true');
    };

    const toggleMegaItem = (item) => {
      if (item.classList.contains('primary-nav__item--open')) {
        closeMegaItem(item);
      } else {
        openMegaItem(item);
      }
    };

    megaItems.forEach((item) => {
      const trigger = item.querySelector('.primary-nav__trigger');

      item.addEventListener('pointerenter', () => {
        if (desktopQuery.matches) {
          openMegaItem(item);
        }
      });

      item.addEventListener('pointerleave', () => {
        if (desktopQuery.matches) {
          scheduleCloseMegaItem(item);
        }
      });

      item.addEventListener('focusin', () => {
        if (desktopQuery.matches) {
          openMegaItem(item);
        }
      });

      item.addEventListener('focusout', (event) => {
        if (desktopQuery.matches && !item.contains(event.relatedTarget)) {
          closeMegaItem(item);
        }
      });

      trigger?.addEventListener('click', (event) => {
        event.preventDefault();

        if (desktopQuery.matches) {
          toggleMegaItem(item);
        }
      });
    });

    document.addEventListener('click', (event) => {
      if (!primaryNav.contains(event.target)) {
        closeAllMegaItems();
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        const openItem = primaryNav.querySelector('.primary-nav__item--open');
        const openTrigger = openItem?.querySelector('.primary-nav__trigger');

        closeAllMegaItems();
        openTrigger?.focus();
      }
    });

    updateAllMegaWrapStates();
    window.addEventListener('resize', requestMegaWrapUpdate);

    if (document.fonts && typeof document.fonts.ready?.then === 'function') {
      document.fonts.ready.then(requestMegaWrapUpdate).catch(() => {});
    }
  }

  let mobileMenuCloseTimer = 0;

  const updateMobileMenuHeight = () => {
    if (toggle.getAttribute('aria-expanded') !== 'true' || panel.hidden) {
      return;
    }

    panel.style.setProperty('--mobile-menu-height', `${panel.scrollHeight}px`);
  };

  const setMenuState = (isOpen) => {
    if (mobileMenuCloseTimer) {
      window.clearTimeout(mobileMenuCloseTimer);
      mobileMenuCloseTimer = 0;
    }

    if (isOpen) {
      panel.hidden = false;

      window.requestAnimationFrame(() => {
        updateMobileMenuHeight();
      });
    } else {
      panel.style.setProperty('--mobile-menu-height', `${panel.scrollHeight}px`);

      window.requestAnimationFrame(() => {
        panel.style.setProperty('--mobile-menu-height', '0px');
      });

      mobileMenuCloseTimer = window.setTimeout(() => {
        panel.hidden = true;
      }, 300);
    }

    root.classList.toggle('site-header--menu-open', isOpen);
    document.documentElement.classList.toggle('has-mobile-menu-open', isOpen);
    toggle.setAttribute('aria-expanded', String(isOpen));
  };

  toggle.addEventListener('click', () => {
    setMenuState(toggle.getAttribute('aria-expanded') !== 'true');
  });

  root.querySelectorAll('.mobile-nav__submenu-toggle').forEach((submenuToggle) => {
    const submenuId = submenuToggle.getAttribute('aria-controls');
    const submenu = submenuId ? document.getElementById(submenuId) : submenuToggle.closest('.mobile-nav__item')?.querySelector('.sub-menu');

    if (!submenu) {
      return;
    }

    submenu.id = submenuId || submenu.id;
    submenu.hidden = false;
    submenu.style.setProperty('--mobile-submenu-height', '0px');

    submenuToggle.addEventListener('click', () => {
      const isExpanded = submenuToggle.getAttribute('aria-expanded') === 'true';
      const nextState = !isExpanded;

      submenuToggle.setAttribute('aria-expanded', String(!isExpanded));
      submenu.style.setProperty('--mobile-submenu-height', nextState ? `${submenu.scrollHeight}px` : '0px');
      submenuToggle.closest('.mobile-nav__item')?.classList.toggle('is-submenu-open', nextState);

      window.requestAnimationFrame(updateMobileMenuHeight);
    });
  });

  window.addEventListener('resize', updateMobileMenuHeight);

  expandableSearches.forEach((expandableSearch) => {
    const searchInput = expandableSearch.querySelector('input[type="search"]');
    const searchSubmit = expandableSearch.querySelector('button[type="submit"]');

    const openSearch = () => {
      expandableSearch.classList.add('is-search-open');
    };

    const closeSearch = () => {
      expandableSearch.classList.remove('is-search-open');
    };

    searchSubmit?.addEventListener('click', (event) => {
      if (!expandableSearch.classList.contains('is-search-open')) {
        event.preventDefault();
        openSearch();
        searchInput?.focus();
      }
    });

    searchInput?.addEventListener('focus', openSearch);

    document.addEventListener('click', (event) => {
      if (!expandableSearch.contains(event.target)) {
        closeSearch();
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && expandableSearch.classList.contains('is-search-open')) {
        closeSearch();
        searchSubmit?.focus();
      }
    });
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
      setMenuState(false);
      toggle.focus();
    }
  });

  announcementClose?.addEventListener('click', () => {
    announcementBanner?.remove();
  });
}
