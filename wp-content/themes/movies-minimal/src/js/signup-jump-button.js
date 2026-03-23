export class SignupJumpButton {
  constructor(trigger, section) {
    this.trigger = trigger instanceof HTMLButtonElement ? trigger : null;
    this.section = section instanceof HTMLElement ? section : null;
  }

  getFirstInput() {
    if (!this.section) {
      return null;
    }

    const input = this.section.querySelector(
      '.aettaec-form input[type="text"], .aettaec-form input[type="email"]'
    );

    return input instanceof HTMLInputElement ? input : null;
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
    if (!this.trigger || !this.section) {
      return;
    }

    this.trigger.addEventListener('click', () => this.handleClick());
  }
}
