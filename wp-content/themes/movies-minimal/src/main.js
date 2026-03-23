import './styles.css';
import { CollectionHeartManager } from './js/collection-heart';
import { SignupJumpButton } from './js/signup-jump-button';
import { ThemeSwitcher } from './js/theme-switcher';

class App {
  constructor() {
    this.themeSwitcher = new ThemeSwitcher(
      document.getElementById('theme-debug-select')
    );
    this.signupJumpButton = new SignupJumpButton(
      document.querySelector('[data-signup-jump]'),
      document.getElementById('home-signup')
    );
    this.collectionHeartManager = new CollectionHeartManager(
      document.querySelectorAll('[data-heart-button]')
    );
  }

  init() {
    this.themeSwitcher.init();
    this.signupJumpButton.init();
    this.collectionHeartManager.init();
  }
}

document.addEventListener('DOMContentLoaded', () => {
  new App().init();
});
