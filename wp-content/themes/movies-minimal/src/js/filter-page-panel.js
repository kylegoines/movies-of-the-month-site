export class FilterPagePanel {
  constructor(button, panel, stateField) {
    this.button = button;
    this.panel = panel;
    this.stateField = stateField;
  }

  get state() {
    return this.stateField?.value === 'closed' ? 'closed' : 'open';
  }

  setState(nextState) {
    const isClosed = nextState === 'closed';
    const url = new URL(window.location.href);

    this.stateField.value = isClosed ? 'closed' : 'open';
    this.panel.classList.toggle('hidden', isClosed);
    this.button.setAttribute('aria-expanded', String(!isClosed));
    this.button.textContent = isClosed ? 'Show Filters' : 'Hide Filters';

    if (isClosed) {
      url.searchParams.set('filters', 'closed');
    } else {
      url.searchParams.delete('filters');
    }

    window.history.replaceState({}, '', url);
  }

  toggle = () => {
    this.setState(this.state === 'closed' ? 'open' : 'closed');
  };

  init() {
    if (!this.button || !this.panel || !this.stateField) {
      return;
    }

    this.button.addEventListener('click', this.toggle);
  }
}
