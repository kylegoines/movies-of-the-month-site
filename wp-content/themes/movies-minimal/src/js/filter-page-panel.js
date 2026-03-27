export class FilterPagePanel {
  constructor(button, panel, stateField) {
    this.button = button;
    this.panel = panel;
    this.stateField = stateField;
    this.onTransitionEnd = this.handleTransitionEnd.bind(this);
  }

  get state() {
    return this.stateField?.value === 'closed' ? 'closed' : 'open';
  }

  handleTransitionEnd(event) {
    if (event.target !== this.panel || event.propertyName !== 'height') {
      return;
    }

    if (this.state === 'open') {
      this.panel.style.height = 'auto';
    }
  }

  collapse(immediate = false) {
    const currentHeight = this.panel.scrollHeight;

    this.panel.dataset.state = 'closed';
    this.panel.style.opacity = '0';
    this.panel.style.pointerEvents = 'none';

    if (immediate) {
      this.panel.style.height = '0px';
      return;
    }

    this.panel.style.height = `${currentHeight}px`;

    window.requestAnimationFrame(() => {
      this.panel.style.height = '0px';
    });
  }

  expand(immediate = false) {
    this.panel.dataset.state = 'open';
    this.panel.style.opacity = '1';
    this.panel.style.pointerEvents = 'auto';

    if (immediate) {
      this.panel.style.height = 'auto';
      return;
    }

    this.panel.style.height = '0px';

    window.requestAnimationFrame(() => {
      this.panel.style.height = `${this.panel.scrollHeight}px`;
    });
  }

  setState(nextState, immediate = false) {
    const isClosed = nextState === 'closed';
    const url = new URL(window.location.href);

    this.stateField.value = isClosed ? 'closed' : 'open';
    this.button.setAttribute('aria-expanded', String(!isClosed));
    this.button.textContent = isClosed ? 'Show Filters' : 'Hide Filters';

    if (isClosed) {
      this.collapse(immediate);
    } else {
      this.expand(immediate);
    }

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

    this.panel.addEventListener('transitionend', this.onTransitionEnd);
    this.setState(this.state, true);
    this.button.addEventListener('click', this.toggle);
  }
}
