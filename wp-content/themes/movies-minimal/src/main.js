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
    this.filterPageHeadingDesktop = new FilterPageHeading(
      document.querySelector('[data-filter-form-desktop]'),
      document.querySelector('[data-filter-heading]'),
      document.querySelector('[data-filter-results]')
    );
    this.filterPageHeadingMobile = new FilterPageHeading(
      document.querySelector('[data-filter-form-mobile]'),
      document.querySelector('[data-filter-heading]'),
      document.querySelector('[data-filter-results]')
    );
    this.filterPagePanelMobile = new FilterPagePanel(
      document.querySelector('[data-filter-toggle-mobile]'),
      document.querySelector('[data-filter-panel-mobile]'),
      document.querySelector('[data-filter-form-mobile] [data-filter-state]'),
      document.querySelector('[data-filter-overlay-mobile]'),
      document.querySelector('[data-filter-close-mobile]')
    );
    this.filterPagePanelDesktop = new FilterPagePanel(
      document.querySelector('[data-filter-toggle-desktop]'),
      document.querySelector('[data-filter-panel-desktop]'),
      document.querySelector('[data-filter-form-desktop] [data-filter-state]')
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
    this.filterPagePanelMobile.init();
    this.filterPagePanelDesktop.init();
    this.filterPageHeadingDesktop.init();
    this.filterPageHeadingMobile.init();
    this.siteMobileNav.init();
    this.siteTitleMarquee.init();
    this.movieHeartManager.init();
    this.spotlightGallery.init();
  }
}

document.addEventListener('DOMContentLoaded', () => {
  new App().init();
});
