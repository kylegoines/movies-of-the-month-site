export class SignupJumpButton {
  constructor(triggers, section) {
    this.triggers = Array.from(triggers);
    this.section = section;
  }

  getFirstInput() {
    return this.section.querySelector(
      '.aettaec-form input[type="text"], .aettaec-form input[type="email"]'
    );
  }

  handleClick() {
    if (!this.section) {
      return;
    }

    const firstInput = this.getFirstInput();

    this.section.scrollIntoView({
      behavior: 'smooth',
      block: 'start',
    });

    if (firstInput) {
      window.setTimeout(() => {
        firstInput.focus({ preventScroll: true });
      }, 350);
    }
  }

  init() {
    if (this.triggers.length === 0 || !this.section) {
      return;
    }

    this.triggers.forEach((trigger) => {
      trigger.addEventListener('click', () => this.handleClick());
    });
  }
}
