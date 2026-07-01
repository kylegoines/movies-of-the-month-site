export class SiteMobileNav {
  constructor(toggleButton, panel) {
    this.toggleButton = toggleButton;
    this.panel = panel;
    this.body = document.body;
    this.root = document.documentElement;
  }

  setOpen(isOpen) {
    if (!this.toggleButton || !this.panel || !this.body || !this.root) {
      return;
    }

    this.toggleButton.dataset.state = isOpen ? 'open' : 'closed';
    this.toggleButton.setAttribute('aria-expanded', String(isOpen));
    this.toggleButton.setAttribute(
      'aria-label',
      isOpen ? 'Close navigation' : 'Open navigation'
    );
    this.panel.setAttribute('aria-hidden', String(!isOpen));
    this.panel.dataset.state = isOpen ? 'open' : 'closed';
    this.body.classList.toggle('site-mobile-nav-open', isOpen);
    this.root.classList.toggle('site-mobile-nav-open', isOpen);
  }

  open = () => {
    this.setOpen(true);
  };

  close = () => {
    this.setOpen(false);
  };

  toggle = () => {
    const isOpen = this.panel?.dataset.state === 'open';
    this.setOpen(!isOpen);
  };

  handleKeydown = (event) => {
    if (event.key !== 'Escape') {
      return;
    }

    this.close();
  };

  init() {
    if (!this.toggleButton || !this.panel) {
      return;
    }

    this.setOpen(false);
    this.toggleButton.addEventListener('click', this.toggle);
    this.panel.querySelectorAll('a, [data-site-mobile-nav-close]').forEach((trigger) => {
      trigger.addEventListener('click', this.close);
    });
    document.addEventListener('keydown', this.handleKeydown);
  }
}
