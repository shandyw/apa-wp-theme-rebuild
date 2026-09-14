function getElementFromSelector(selector) {
  if (!selector || typeof selector !== 'string') {
    return null;
  }

  if (selector.startsWith('#')) {
    return document.getElementById(selector.slice(1));
  }

  return document.querySelector(selector);
}

function updateToggleAll(wrapper) {
  if (!wrapper) {
    return;
  }

  const panels = Array.from(wrapper.querySelectorAll('.accordion-panel'));
  const toggle = wrapper.querySelector('.accordion-toggle-link');

  if (!toggle || panels.length === 0) {
    return;
  }

  const allOpen = panels.every((panel) => !panel.hidden);

  toggle.textContent = allOpen ? 'Hide all' : 'Show all';
  toggle.setAttribute('data-state', allOpen ? 'open' : 'closed');
}

function finalizeOpen(panel) {
  panel.style.height = 'auto';
  panel.dataset.animating = 'false';
}

function setPanel(trigger, panel, isOpen) {
  const finishCurrentAnimation = panel._accordionAnimationFinish;

  if (typeof finishCurrentAnimation === 'function') {
    finishCurrentAnimation();
  }

  trigger.setAttribute('aria-expanded', String(isOpen));

  if (isOpen) {
    panel.hidden = false;
    panel.dataset.animating = 'true';
    panel.style.height = '0px';

    requestAnimationFrame(() => {
      const targetHeight = panel.scrollHeight;

      panel.style.height = `${targetHeight}px`;
    });

    const handleOpenEnd = (event) => {
      if (event.target !== panel || event.propertyName !== 'height') {
        return;
      }

      panel.removeEventListener('transitionend', handleOpenEnd);
      panel._accordionAnimationFinish = null;
      finalizeOpen(panel);
    };

    panel._accordionAnimationFinish = () => {
      panel.removeEventListener('transitionend', handleOpenEnd);
      panel._accordionAnimationFinish = null;
      finalizeOpen(panel);
    };

    panel.addEventListener('transitionend', handleOpenEnd);
    return;
  }

  if (panel.hidden) {
    panel.style.height = '0px';
    panel.dataset.animating = 'false';
    return;
  }

  panel.dataset.animating = 'true';
  panel.style.height = `${panel.scrollHeight}px`;

  requestAnimationFrame(() => {
    panel.style.height = '0px';
  });

  const handleCloseEnd = (event) => {
    if (event.target !== panel || event.propertyName !== 'height') {
      return;
    }

    panel.removeEventListener('transitionend', handleCloseEnd);
    panel._accordionAnimationFinish = null;
    panel.hidden = true;
    panel.dataset.animating = 'false';
  };

  panel._accordionAnimationFinish = () => {
    panel.removeEventListener('transitionend', handleCloseEnd);
    panel._accordionAnimationFinish = null;
    panel.hidden = true;
    panel.dataset.animating = 'false';
  };

  panel.addEventListener('transitionend', handleCloseEnd);
}

function closeSiblingPanels(wrapper, activeTrigger) {
  if (!wrapper) {
    return;
  }

  const triggers = wrapper.querySelectorAll('.accordion-trigger[aria-expanded="true"]');

  triggers.forEach((button) => {
    if (button === activeTrigger) {
      return;
    }

    const panel = getElementFromSelector(button.getAttribute('data-accordion-target'));

    if (panel) {
      setPanel(button, panel, false);
    }
  });
}

export function initAccordions(context = document) {
  const root = context === document ? document : context.ownerDocument || document;

  if (root.documentElement.dataset.accordionsReady === 'true') {
    return;
  }

  root.documentElement.dataset.accordionsReady = 'true';

  root.addEventListener('click', (event) => {
    const trigger = event.target.closest('.accordion-trigger');

    if (trigger) {
      const panel = getElementFromSelector(trigger.getAttribute('data-accordion-target'));
      const wrapper = trigger.closest('.accordion-wrapper');

      if (!panel) {
        return;
      }

      event.preventDefault();

      const expanded = trigger.getAttribute('aria-expanded') === 'true';

      if (!expanded) {
        closeSiblingPanels(wrapper, trigger);
      }

      setPanel(trigger, panel, !expanded);
      updateToggleAll(wrapper);
      return;
    }

    const toggle = event.target.closest('.accordion-toggle-link');

    if (!toggle) {
      return;
    }

    const wrapper = getElementFromSelector(toggle.getAttribute('data-accordion-toggle'));

    if (!wrapper) {
      return;
    }

    event.preventDefault();

    const openAll = toggle.getAttribute('data-state') !== 'open';
    const triggers = wrapper.querySelectorAll('.accordion-trigger');

    triggers.forEach((button) => {
      const panel = getElementFromSelector(button.getAttribute('data-accordion-target'));

      if (panel) {
        setPanel(button, panel, openAll);
      }
    });

    updateToggleAll(wrapper);
  });
}
