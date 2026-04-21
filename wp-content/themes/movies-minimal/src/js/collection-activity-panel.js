export class CollectionActivityPanel {
  constructor(button, panel, overlay, closeButton) {
    this.button = button;
    this.panel = panel;
    this.overlay = overlay;
    this.closeButton = closeButton;
    this.body = document.body;
  }

  setState(isOpen) {
    if (!this.button || !this.panel) {
      return;
    }

    this.button.setAttribute('aria-expanded', String(isOpen));
    this.panel.dataset.state = isOpen ? 'open' : 'closed';
    this.panel.setAttribute('aria-hidden', String(!isOpen));

    if (this.overlay) {
      this.overlay.dataset.state = isOpen ? 'open' : 'closed';
      this.overlay.setAttribute('aria-hidden', String(!isOpen));
    }

    this.body?.classList.toggle('collection-activity-panel-open', isOpen);
  }

  open = () => {
    this.setState(true);
  };

  close = () => {
    this.setState(false);
  };

  toggle = () => {
    const isOpen = this.panel?.dataset.state === 'open';
    this.setState(!isOpen);
  };

  handleKeydown = (event) => {
    if (event.key !== 'Escape' || this.panel?.dataset.state !== 'open') {
      return;
    }

    this.close();
    this.button?.focus();
  };

  init() {
    if (!this.button || !this.panel) {
      return;
    }

    this.setState(false);
    this.button.addEventListener('click', this.toggle);
    this.overlay?.addEventListener('click', this.close);
    this.closeButton?.addEventListener('click', this.close);
    document.addEventListener('keydown', this.handleKeydown);
  }
}
