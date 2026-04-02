export class FilterPageHeading {
  constructor(form, heading, results) {
    this.form = form;
    this.heading = heading;
    this.results = results;
    this.textNode = this.heading?.querySelector('span') ?? this.heading;
    this.baseText = this.heading?.dataset.baseText || 'Movies that are...';
    this.selects = Array.from(this.form?.querySelectorAll('[data-filter-select]') || []);
    this.isPending = false;
    this.stateField = this.form?.querySelector('[data-filter-state]') || null;
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
        label:
          name === 'category' || name === 'movie_author'
            ? label
            : label.toLowerCase(),
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
      this.syncForms();
      this.updateHeading();
      void this.fetchResults();
    });

    return button;
  }

  appendText(text) {
    this.textNode.append(document.createTextNode(text));
  }

  appendMovieLead(movieLead, authorFilter) {
    this.appendText(movieLead);

    if (!authorFilter) {
      return;
    }

    this.appendText(' recommended by ');
    this.textNode.append(this.createTermButton(authorFilter));
  }

  appendFilterList(filters) {
    filters.forEach((filter, index) => {
      if (index > 0) {
        this.appendText(
          index === filters.length - 1 ? ' and ' : ', '
        );
      }

      this.textNode.append(this.createTermButton(filter));
    });
  }

  syncForms() {
    const formData = new FormData(this.form);

    document.querySelectorAll('[data-filter-form-sync]').forEach((form) => {
      if (form === this.form) {
        return;
      }

      Array.from(form.elements).forEach((element) => {
        if (!element.name || !('value' in element)) {
          return;
        }

        element.value = formData.get(element.name)?.toString() || '';
      });
    });
  }

  buildRequestUrl() {
    const requestUrl = new URL(this.form.action, window.location.origin);
    const formData = new FormData(this.form);

    requestUrl.search = '';

    formData.forEach((value, key) => {
      if (typeof value !== 'string' || value === '') {
        return;
      }

      requestUrl.searchParams.set(key, value);
    });

    return requestUrl;
  }

  async fetchResults() {
    if (!this.results || this.isPending) {
      return;
    }

    const requestUrl = this.buildRequestUrl();

    this.isPending = true;
    this.results.style.opacity = '0.5';

    try {
      const response = await fetch(requestUrl.toString(), {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
        },
      });
      const markup = await response.text();
      const nextDocument = new DOMParser().parseFromString(markup, 'text/html');
      const nextResults = nextDocument.querySelector('[data-filter-results]');
      const nextStateField = nextDocument.querySelector('[data-filter-state]');

      if (!nextResults) {
        return;
      }

      this.results.innerHTML = nextResults.innerHTML;

      if (this.stateField && nextStateField instanceof HTMLInputElement) {
        this.stateField.value = nextStateField.value;
      }

      this.syncForms();
      window.history.replaceState({}, '', requestUrl);
    } finally {
      this.results.style.opacity = '';
      this.isPending = false;
    }
  }

  updateHeading() {
    if (!this.textNode) {
      return;
    }

    const selectedFilters = this.getSelectedFilters();
    const categoryFilter = selectedFilters.find(({ name }) => name === 'category');
    const authorFilter = selectedFilters.find(({ name }) => name === 'movie_author');
    const pacingFilter = selectedFilters.find(({ name }) => name === 'pacing');
    const nonPacingFilters = selectedFilters.filter(
      ({ name }) =>
        name !== 'pacing' && name !== 'category' && name !== 'movie_author'
    );
    const movieLead = categoryFilter
      ? `${categoryFilter.label} movies`
      : 'Movies';

    this.textNode.textContent = '';

    if (selectedFilters.length === 0) {
      this.textNode.textContent = this.baseText;
      return;
    }

    if (authorFilter && !categoryFilter && !pacingFilter && nonPacingFilters.length === 0) {
      this.appendText('Movies recommended by ');
      this.textNode.append(this.createTermButton(authorFilter));
      return;
    }

    if (categoryFilter && !pacingFilter && nonPacingFilters.length === 0) {
      this.appendMovieLead(movieLead, authorFilter);
      return;
    }

    if (pacingFilter && nonPacingFilters.length === 0) {
      this.appendMovieLead(movieLead, authorFilter);
      this.appendText(' that have ');
      this.textNode.append(this.createTermButton({
        name: pacingFilter.name,
        label: pacingFilter.label,
        suffix: ' pacing',
      }));
      return;
    }

    if (pacingFilter) {
      this.appendMovieLead(movieLead, authorFilter);
      this.appendText(' that are ');
      this.appendFilterList(nonPacingFilters);
      this.appendText(' with ');
      this.textNode.append(this.createTermButton({
        name: pacingFilter.name,
        label: pacingFilter.label,
        suffix: ' pacing',
      }));
      return;
    }

    this.appendMovieLead(movieLead, authorFilter);
    this.appendText(' that are ');
    this.appendFilterList(nonPacingFilters);
  }

  init() {
    if (!this.form || !this.heading || !this.results) {
      return;
    }

    this.updateHeading();

    this.form.addEventListener('submit', (event) => {
      event.preventDefault();
      this.syncForms();
      this.updateHeading();
      void this.fetchResults();
    });

    this.selects.forEach((select) => {
      select.addEventListener('change', () => {
        this.syncForms();
        this.updateHeading();
        void this.fetchResults();
      });
    });
  }
}
