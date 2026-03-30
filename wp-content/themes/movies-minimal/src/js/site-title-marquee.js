export class SiteTitleMarquee {
  constructor(track, button) {
    this.track = track;
    this.button = button;
    this.isPaused = false;
    this.isHoverPaused = false;
  }

  setPaused(isPaused) {
    if (!this.track || !this.button) {
      return;
    }

    this.isPaused = isPaused;
    this.track.dataset.state = isPaused ? 'paused' : 'playing';
    this.button.setAttribute('aria-pressed', String(isPaused));
    this.button.setAttribute(
      'aria-label',
      isPaused ? 'Play marquee' : 'Pause marquee'
    );
    this.button.textContent = isPaused ? '>' : '||';
  }

  toggle = () => {
    this.setPaused(!this.isPaused);
  };

  handleMouseEnter = () => {
    this.isHoverPaused = true;
    this.track.dataset.hoverPaused = 'true';
  };

  handleMouseLeave = () => {
    this.isHoverPaused = false;
    this.track.dataset.hoverPaused = 'false';
  };

  init() {
    if (!this.track || !this.button) {
      return;
    }

    this.setPaused(false);
    this.button.addEventListener('click', this.toggle);
    this.track.addEventListener('mouseenter', this.handleMouseEnter);
    this.track.addEventListener('mouseleave', this.handleMouseLeave);
  }
}
