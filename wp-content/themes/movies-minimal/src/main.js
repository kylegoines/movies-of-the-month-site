import './styles.css';
import { MovieHeartManager } from './js/collection-heart';
import { FilterPageHeading } from './js/filter-page-heading';
import { FilterPagePanel } from './js/filter-page-panel';
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
      document.querySelector('[data-filter-heading]'),
      document.querySelector('[data-filter-results]')
    );
    this.filterPagePanel = new FilterPagePanel(
      document.querySelector('[data-filter-toggle]'),
      document.querySelector('[data-filter-panel]'),
      document.querySelector('[data-filter-state]')
    );
    this.movieHeartManager = new MovieHeartManager(
      document.querySelectorAll('[data-heart-button]')
    );
  }

  init() {
    this.themeSwitcher.init();
    this.signupJumpButton.init();
    this.filterPagePanel.init();
    this.filterPageHeading.init();
    this.movieHeartManager.init();
  }
}

document.addEventListener('DOMContentLoaded', () => {
  new App().init();
});
