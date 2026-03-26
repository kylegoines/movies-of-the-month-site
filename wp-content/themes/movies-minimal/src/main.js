import './styles.css';
import { CollectionHeartManager } from './js/collection-heart';
import { FilterPageHeading } from './js/filter-page-heading';
import { SignupJumpButton } from './js/signup-jump-button';
import { ThemeSwitcher } from './js/theme-switcher';

class App {
  constructor() {
    this.themeSwitcher = new ThemeSwitcher(
      document.getElementById('theme-debug-select')
    );
    this.signupJumpButton = new SignupJumpButton(
      document.querySelectorAll('[data-signup-jump]'),
      document.getElementById('home-signup')
    );
    this.filterPageHeading = new FilterPageHeading(
      document.querySelector('[data-filter-form]'),
      document.querySelector('[data-filter-heading]')
    );
    this.collectionHeartManager = new CollectionHeartManager(
      document.querySelectorAll('[data-heart-button]')
    );
  }

  init() {
    this.themeSwitcher.init();
    this.signupJumpButton.init();
    this.filterPageHeading.init();
    this.collectionHeartManager.init();
  }
}

document.addEventListener('DOMContentLoaded', () => {
  new App().init();
});
