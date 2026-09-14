export function initContactInformation(context = document) {
  const modules = context.querySelectorAll('[data-contact-departments]');

  modules.forEach((module) => {
    const select = module.querySelector('[data-contact-department-select]');
    const panels = module.querySelectorAll('[data-contact-department-panel]');

    if (!select || !panels.length || select.dataset.contactDepartmentReady === 'true') {
      return;
    }

    select.dataset.contactDepartmentReady = 'true';

    const updatePanels = () => {
      const selectedDepartment = select.value;

      panels.forEach((panel) => {
        const isActive = selectedDepartment && panel.dataset.department === selectedDepartment;

        panel.hidden = !isActive;
        panel.classList.toggle('is-active', Boolean(isActive));
      });
    };

    select.addEventListener('change', updatePanels);
    updatePanels();
  });
}
