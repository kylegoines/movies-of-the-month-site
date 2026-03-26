export class FilterPageHeading {
  constructor(form, heading) {
    this.form = form;
    this.heading = heading;
    this.textNode = this.heading?.querySelector('span') ?? this.heading;
    this.baseText = this.heading?.dataset.baseText || 'Movies that are...';
    this.selects = Array.from(this.form?.querySelectorAll('[data-filter-select]') || []);
  }

  getSelectedFilters() {
    return this.selects
      .map((select) => ({
        name: select.name || '',
        label: select.options[select.selectedIndex]?.text || '',
      }))
      .filter(
        ({ label }) => label !== '' && label !== 'Any' && label !== 'Select'
      )
      .map(({ name, label }) => ({
        name,
        label: label.toLowerCase(),
      }));
  }

  updateHeading() {
    if (!this.textNode) {
      return;
    }

    const selectedFilters = this.getSelectedFilters();
    const pacingFilter = selectedFilters.find(({ name }) => name === 'pacing');
    const nonPacingLabels = selectedFilters
      .filter(({ name }) => name !== 'pacing')
      .map(({ label }) => label);

    if (selectedFilters.length === 0) {
      this.textNode.textContent = this.baseText;
      return;
    }

    if (pacingFilter && nonPacingLabels.length === 0) {
      this.textNode.textContent = `Movies that have ${pacingFilter.label} pacing`;
      return;
    }

    if (pacingFilter) {
      this.textNode.textContent = `Movies that are ${nonPacingLabels.join(', ')} with ${pacingFilter.label} pacing`;
      return;
    }

    this.textNode.textContent = `Movies that are ${nonPacingLabels.join(', ')}`;
  }

  init() {
    if (!this.form || !this.heading) {
      return;
    }

    this.updateHeading();

    this.selects.forEach((select) => {
      select.addEventListener('change', () => this.updateHeading());
    });
  }
}
