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

  createTermButton({ name, label, suffix = '' }) {
    const button = document.createElement('button');
    button.className = 'filter-heading__term';
    button.type = 'button';
    button.dataset.filterRemove = name;
    button.textContent = `${label}${suffix}`;
    button.addEventListener('click', () => {
      const select = this.selects.find((entry) => entry.name === name);

      if (!select) {
        return;
      }

      select.value = '';
      this.form.requestSubmit();
    });

    return button;
  }

  appendText(text) {
    this.textNode.append(document.createTextNode(text));
  }

  updateHeading() {
    if (!this.textNode) {
      return;
    }

    const selectedFilters = this.getSelectedFilters();
    const pacingFilter = selectedFilters.find(({ name }) => name === 'pacing');
    const nonPacingFilters = selectedFilters.filter(({ name }) => name !== 'pacing');

    this.textNode.textContent = '';

    if (selectedFilters.length === 0) {
      this.textNode.textContent = this.baseText;
      return;
    }

    if (pacingFilter && nonPacingFilters.length === 0) {
      this.appendText('Movies that have ');
      this.textNode.append(this.createTermButton({
        name: pacingFilter.name,
        label: pacingFilter.label,
        suffix: ' pacing',
      }));
      return;
    }

    if (pacingFilter) {
      this.appendText('Movies that are ');
      nonPacingFilters.forEach((filter, index) => {
        if (index > 0) {
          this.appendText(', ');
        }

        this.textNode.append(this.createTermButton(filter));
      });
      this.appendText(' with ');
      this.textNode.append(this.createTermButton({
        name: pacingFilter.name,
        label: pacingFilter.label,
        suffix: ' pacing',
      }));
      return;
    }

    this.appendText('Movies that are ');
    nonPacingFilters.forEach((filter, index) => {
      if (index > 0) {
        this.appendText(', ');
      }

      this.textNode.append(this.createTermButton(filter));
    });
  }

  init() {
    if (!this.form || !this.heading) {
      return;
    }

    this.updateHeading();

    this.selects.forEach((select) => {
      select.addEventListener('change', () => {
        this.updateHeading();
        this.form.requestSubmit();
      });
    });
  }
}
