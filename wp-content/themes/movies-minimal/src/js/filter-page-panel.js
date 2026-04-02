export class FilterPagePanel {
  constructor(button, panel, stateField, overlay, closeButton) {
    this.button = button;
    this.panel = panel;
    this.stateField = stateField;
    this.overlay = overlay;
    this.closeButton = closeButton;
    this.body = document.body;
    this.mode = this.panel?.dataset.filterPanelMode || 'accordion';
  }

  get state() {
    return this.stateField?.value === 'closed' ? 'closed' : 'open';
  }

  syncUrl(isClosed) {
    const url = new URL(window.location.href);

    if (isClosed) {
      url.searchParams.set('filters', 'closed');
    } else {
      url.searchParams.delete('filters');
    }

    window.history.replaceState({}, '', url);
  }

  syncLayout() {
    if (!this.panel) {
      return;
    }

    if (this.mode === 'accordion') {
      this.panel.style.height =
        this.state === 'closed' ? '0px' : `${this.panel.scrollHeight}px`;
    }
  }

  setState(nextState, { updateUrl = true } = {}) {
    const isClosed = nextState === 'closed';

    this.stateField.value = isClosed ? 'closed' : 'open';
    this.button?.setAttribute('aria-expanded', String(!isClosed));
    this.panel.dataset.state = isClosed ? 'closed' : 'open';
    this.panel.setAttribute('aria-hidden', String(isClosed));

    if (this.overlay) {
      this.overlay.dataset.state = isClosed ? 'closed' : 'open';
      this.overlay.setAttribute('aria-hidden', String(isClosed));
    }

    if (this.mode === 'drawer') {
      this.body?.classList.toggle('filter-panel-open', !isClosed);
    }

    this.syncLayout();

    if (updateUrl) {
      this.syncUrl(isClosed);
    }
  }

  open = () => {
    this.setState('open');
  };

  close = () => {
    this.setState('closed');
  };

  toggle = () => {
    this.setState(this.state === 'closed' ? 'open' : 'closed');
  };

  handleKeydown = (event) => {
    if (event.key !== 'Escape' || this.mode !== 'drawer' || this.state === 'closed') {
      return;
    }

    this.close();
    this.button?.focus();
  };

  init() {
    if (!this.panel || !this.stateField) {
      return;
    }

    this.setState(this.state, { updateUrl: false });
    this.button?.addEventListener('click', this.toggle);
    this.overlay?.addEventListener('click', this.close);
    this.closeButton?.addEventListener('click', this.close);
    document.addEventListener('keydown', this.handleKeydown);
    window.addEventListener('resize', () => this.syncLayout());
  }
}
