function isMobileViewport() {
  return window.matchMedia('(max-width: 767.98px)').matches;
}

function collapseSiblingCards(card) {
  const grid = card.closest('.hover-boxes__grid');

  if (!(grid instanceof HTMLElement)) {
    return;
  }

  grid.querySelectorAll('.hover-boxes__card.is-expanded').forEach((sibling) => {
    if (sibling !== card && sibling instanceof HTMLElement) {
      sibling.classList.remove('is-expanded');
    }
  });
}

export function initHoverBoxes(context = document) {
  const cards = context.querySelectorAll('.hover-boxes__card:not(.hover-boxes__card--intro)');

  cards.forEach((card) => {
    if (!(card instanceof HTMLElement) || card.dataset.hoverBoxesReady === 'true') {
      return;
    }

    card.dataset.hoverBoxesReady = 'true';

    card.addEventListener('click', (event) => {
      if (!isMobileViewport()) {
        return;
      }

      const target = event.target;

      if (!(target instanceof HTMLElement)) {
        return;
      }

      const link = target.closest('.hover-boxes__item-link');

      if (link instanceof HTMLAnchorElement) {
        return;
      }

      event.preventDefault();

      const isExpanded = card.classList.contains('is-expanded');

      collapseSiblingCards(card);
      card.classList.toggle('is-expanded', !isExpanded);
    });
  });

  if (document.documentElement.dataset.hoverBoxesDocumentReady === 'true') {
    return;
  }

  document.documentElement.dataset.hoverBoxesDocumentReady = 'true';

  document.addEventListener('click', (event) => {
    if (!isMobileViewport()) {
      return;
    }

    const target = event.target;

    if (target instanceof HTMLElement && target.closest('.hover-boxes__card')) {
      return;
    }

    document.querySelectorAll('.hover-boxes__card.is-expanded').forEach((card) => {
      if (card instanceof HTMLElement) {
        card.classList.remove('is-expanded');
      }
    });
  });
}
