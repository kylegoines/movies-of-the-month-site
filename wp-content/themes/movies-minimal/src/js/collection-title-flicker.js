export class CollectionTitleFlicker {
  constructor(titles) {
    this.desktopQuery = window.matchMedia('(min-width: 64rem)');
    this.titles = Array.from(titles).map((title) => {
      const imageData = title.dataset.collectionTitleImages || '[]';

      try {
        const images = JSON.parse(imageData).filter(Boolean);

        return images.length > 0
          ? {
              element: title,
              images,
              index: 0,
              intervalId: null
            }
          : null;
      } catch {
        return null;
      }
    }).filter(Boolean);
  }

  init() {
    if (this.titles.length === 0) {
      return;
    }

    this.desktopQuery.addEventListener('change', (event) => {
      if (!event.matches) {
        this.titles.forEach((title) => this.stop(title));
      }
    });

    this.titles.forEach((title) => {
      title.element.addEventListener('mouseenter', () => this.start(title));
      title.element.addEventListener('mouseleave', () => this.stop(title));
      title.element.addEventListener('focusin', () => this.start(title));
      title.element.addEventListener('focusout', () => this.stop(title));
    });
  }

  start(title) {
    if (!this.desktopQuery.matches) {
      this.stop(title);
      return;
    }

    if (title.intervalId !== null) {
      return;
    }

    title.element.classList.add('is-flickering');
    this.render(title);

    title.intervalId = window.setInterval(() => {
      title.index = (title.index + 1) % title.images.length;
      this.render(title);
    }, 140);
  }

  stop(title) {
    if (title.intervalId !== null) {
      window.clearInterval(title.intervalId);
      title.intervalId = null;
    }

    title.index = 0;
    title.element.classList.remove('is-flickering');
    title.element.style.removeProperty('--collection-title-image');
  }

  render(title) {
    title.element.style.setProperty(
      '--collection-title-image',
      `url("${title.images[title.index]}")`
    );
  }
}
