import './styles.css';
import { MovieHeartManager } from './js/collection-heart';
import { FilterPageHeading } from './js/filter-page-heading';
import { FilterPagePanel } from './js/filter-page-panel';
import { SiteMobileNav } from './js/site-mobile-nav';
import { SiteTitleMarquee } from './js/site-title-marquee';
import { SignupJumpButton } from './js/signup-jump-button';
import { SpotlightGallery } from './js/spotlight-gallery';
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
      document.querySelector('[data-filter-state]'),
      document.querySelector('[data-filter-overlay]'),
      document.querySelector('[data-filter-close]')
    );
    this.siteTitleMarquee = new SiteTitleMarquee(
      document.querySelector('[data-site-title-marquee-track]'),
      document.querySelector('[data-site-title-marquee-toggle]')
    );
    this.siteMobileNav = new SiteMobileNav(
      document.querySelector('[data-site-mobile-nav-toggle]'),
      document.querySelector('[data-site-mobile-nav]')
    );
    this.movieHeartManager = new MovieHeartManager(
      document.querySelectorAll('[data-heart-button]')
    );
    this.spotlightGallery = new SpotlightGallery(
      document.querySelectorAll('[data-spotlight-gallery]')
    );
  }

  init() {
    this.themeSwitcher.init();
    this.signupJumpButton.init();
    this.filterPagePanel.init();
    this.filterPageHeading.init();
    this.siteMobileNav.init();
    this.siteTitleMarquee.init();
    this.movieHeartManager.init();
    this.spotlightGallery.init();
  }
}

document.addEventListener('DOMContentLoaded', () => {
  new App().init();
});
