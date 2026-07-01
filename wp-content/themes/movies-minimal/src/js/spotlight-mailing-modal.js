export class SpotlightMailingModal {
  constructor(openButtons, modal, overlay, closeButton) {
    this.openButtons = Array.from(openButtons ?? []);
    this.modal = modal;
    this.overlay = overlay;
    this.closeButton = closeButton;
    this.body = document.body;
    this.lastTrigger = null;
  }

  setState(isOpen) {
    if (!this.modal) {
      return;
    }

    this.modal.dataset.state = isOpen ? 'open' : 'closed';
    this.modal.setAttribute('aria-hidden', String(!isOpen));
    this.overlay?.setAttribute('aria-hidden', String(!isOpen));
    this.overlay && (this.overlay.dataset.state = isOpen ? 'open' : 'closed');
    this.body?.classList.toggle('spotlight-mailing-modal-open', isOpen);

    this.openButtons.forEach((button) => {
      button.setAttribute('aria-expanded', String(isOpen));
    });
  }

  open = (event) => {
    this.lastTrigger = event?.currentTarget instanceof HTMLElement
      ? event.currentTarget
      : null;
    this.setState(true);
    this.closeButton?.focus();
  };

  close = () => {
    this.setState(false);
    this.lastTrigger?.focus();
  };

  handleKeydown = (event) => {
    if (event.key !== 'Escape' || this.modal?.dataset.state !== 'open') {
      return;
    }

    this.close();
  };

  init() {
    if (!this.modal || this.openButtons.length === 0) {
      return;
    }

    this.setState(false);
    this.openButtons.forEach((button) => {
      button.addEventListener('click', this.open);
    });
    this.overlay?.addEventListener('click', this.close);
    this.closeButton?.addEventListener('click', this.close);
    document.addEventListener('keydown', this.handleKeydown);

    const status = new URLSearchParams(window.location.search).get('mailing_signup_status');
    if (status) {
      this.setState(true);
    }
  }
}
