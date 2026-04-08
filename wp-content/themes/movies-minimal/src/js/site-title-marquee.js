export class SiteTitleMarquee {
  constructor(track, button) {
    this.track = track;
    this.button = button;
    this.isPaused = track?.dataset.state === 'paused';
    this.isHoverPaused = false;
    this.cookieMaxAge = 60 * 60 * 24 * 30;
    this.progress = this.getStoredProgress();
  }

  getStoredProgress() {
    const rawProgress = Number(this.track?.dataset.marqueeProgress ?? '0');

    if (!Number.isFinite(rawProgress)) {
      return 0;
    }

    return Math.min(Math.max(rawProgress, 0), 1);
  }

  setCookie(name, value, maxAge = this.cookieMaxAge) {
    document.cookie = `${name}=${value}; path=/; max-age=${maxAge}; SameSite=Lax`;
  }

  clearCookie(name) {
    document.cookie = `${name}=; path=/; max-age=0; SameSite=Lax`;
  }

  setProgress(progress) {
    if (!this.track) {
      return;
    }

    this.progress = Math.min(Math.max(progress, 0), 1);
    this.track.dataset.marqueeProgress = this.progress.toFixed(5);
    this.track.style.setProperty(
      '--site-title-marquee-progress',
      this.progress.toFixed(5)
    );
  }

  setFrozenTransform(transformValue = '') {
    if (!this.track) {
      return;
    }

    if (transformValue === '') {
      this.track.style.removeProperty('--site-title-marquee-transform');
      return;
    }

    this.track.style.setProperty('--site-title-marquee-transform', transformValue);
  }

  getCurrentProgress() {
    if (!this.track) {
      return this.progress;
    }

    const computedStyle = window.getComputedStyle(this.track);
    const transformValue = computedStyle.transform;
    const travelDistance = this.track.offsetWidth * 0.2;

    if (!transformValue || transformValue === 'none' || travelDistance <= 0) {
      return this.progress;
    }

    let translateX = 0;

    try {
      const matrix = new DOMMatrixReadOnly(transformValue);
      translateX = matrix.m41;
    } catch (error) {
      const matrixMatch = transformValue.match(/matrix\(([^)]+)\)/);

      if (!matrixMatch) {
        return this.progress;
      }

      const values = matrixMatch[1].split(',').map((value) => Number(value.trim()));
      translateX = Number.isFinite(values[4]) ? values[4] : 0;
    }

    const progress = ((-translateX / travelDistance) % 1 + 1) % 1;

    return Math.min(Math.max(progress, 0), 1);
  }

  persistPausedState(isPaused) {
    if (isPaused) {
      this.setCookie('movies_site_title_marquee_paused', '1');
      this.setCookie(
        'movies_site_title_marquee_progress',
        this.progress.toFixed(5)
      );
      return;
    }

    this.clearCookie('movies_site_title_marquee_paused');
    this.clearCookie('movies_site_title_marquee_progress');
  }

  setPaused(isPaused, syncCurrentProgress = false) {
    if (!this.track || !this.button) {
      return;
    }

    if (isPaused && syncCurrentProgress) {
      const computedStyle = window.getComputedStyle(this.track);
      this.setFrozenTransform(computedStyle.transform === 'none' ? '' : computedStyle.transform);
      this.setProgress(this.getCurrentProgress());
    } else if (!isPaused) {
      this.setFrozenTransform('');
    }

    this.isPaused = isPaused;
    this.track.dataset.state = isPaused ? 'paused' : 'playing';
    this.button.setAttribute('aria-pressed', String(isPaused));
    this.button.setAttribute(
      'aria-label',
      isPaused ? 'Play animation' : 'Pause animation'
    );
    this.button.textContent = isPaused ? 'Play animation' : 'Pause animation';
    this.persistPausedState(isPaused);
  }

  toggle = () => {
    this.setPaused(!this.isPaused, !this.isPaused);
    this.button?.blur();
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

    this.setProgress(this.progress);
    this.setPaused(this.isPaused);
    this.button.addEventListener('click', this.toggle);
    this.track.addEventListener('mouseenter', this.handleMouseEnter);
    this.track.addEventListener('mouseleave', this.handleMouseLeave);
  }
}
