export class SpotlightGallery {
  constructor(galleries) {
    this.galleries = Array.from(galleries, (gallery) => ({
      root: gallery,
      slides: Array.from(gallery.querySelectorAll('.spotlight-gallery__slide')),
      activeIndex: 0,
      intervalId: null,
    }));
  }

  setActive(gallery, nextIndex) {
    gallery.activeIndex = nextIndex;

    gallery.slides.forEach((slide, index) => {
      slide.classList.toggle('spotlight-gallery__slide--active', index === nextIndex);
    });
  }

  start(gallery) {
    if (gallery.slides.length < 2 || gallery.intervalId !== null) {
      return;
    }

    if (gallery.activeIndex === 0) {
      this.setActive(gallery, 1);
    }

    gallery.intervalId = window.setInterval(() => {
      const nextIndex =
        gallery.activeIndex >= gallery.slides.length - 1
          ? 1
          : gallery.activeIndex + 1;
      this.setActive(gallery, nextIndex);
    }, 230);
  }

  stop(gallery) {
    if (gallery.intervalId !== null) {
      window.clearInterval(gallery.intervalId);
      gallery.intervalId = null;
    }

    this.setActive(gallery, 0);
  }

  init() {
    this.galleries.forEach((gallery) => {
      if (gallery.slides.length < 2) {
        return;
      }

      gallery.root.addEventListener('mouseenter', () => this.start(gallery));
      gallery.root.addEventListener('mouseleave', () => this.stop(gallery));
      gallery.root.addEventListener('focusin', () => this.start(gallery));
      gallery.root.addEventListener('focusout', () => this.stop(gallery));
    });
  }
}
