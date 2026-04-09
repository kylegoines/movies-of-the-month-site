export class FeaturedPullquote {
  constructor(elements) {
    this.elements = Array.from(elements || []);
  }

  updateElement(element) {
    if (!(element instanceof HTMLElement)) {
      return;
    }

    if (window.innerWidth < 1024) {
      element.dataset.fits = 'false';
      return;
    }

    element.dataset.fits = 'true';

    const rect = element.getBoundingClientRect();
    const fitsViewport = rect.left >= 0 && rect.right <= window.innerWidth;

    element.dataset.fits = fitsViewport ? 'true' : 'false';
  }

  updateAll = () => {
    this.elements.forEach((element) => this.updateElement(element));
  };

  init() {
    if (this.elements.length === 0) {
      return;
    }

    this.updateAll();
    window.addEventListener('resize', this.updateAll);

    if (document.fonts?.ready) {
      document.fonts.ready.then(this.updateAll).catch(() => {});
    }
  }
}
