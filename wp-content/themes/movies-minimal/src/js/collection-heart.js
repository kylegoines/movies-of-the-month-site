// Added in wp-content/themes/movies-theme/functions.php via wp_localize_script().
// {
//   ajaxUrl: WordPress AJAX endpoint URL,
//   heartNonce: nonce checked by check_ajax_referer() for heart requests,
// }
const heartAPIConfig = movieApp;

class CollectionHeartButton {
  constructor(button) {
    this.button = button;
    this.countNode = this.button.querySelector('[data-heart-count]');
    this.iconNode = this.button.querySelector('.collection-heart__icon');
  }

  get postId() {
    return this.button.dataset.postId ?? '';
  }

  get isLiked() {
    return this.button.getAttribute('aria-pressed') === 'true';
  }

  get count() {
    const text = this.countNode.textContent;
    return Number.parseInt(text ?? '0', 10) || 0;
  }

  setPending(isPending) {
    if (isPending) {
      this.button.dataset.pending = '1';
      return;
    }

    delete this.button.dataset.pending;
  }

  setState({ liked, count }) {
    this.button.setAttribute('aria-pressed', String(liked));
    this.countNode.textContent = String(count);
    this.iconNode.textContent = liked ? '♥' : '♡';
  }

  async sendRequest(nextLiked) {
    const response = await fetch(heartAPIConfig.ajaxUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
      },
      body: new URLSearchParams({
        action: 'movies_theme_toggle_heart',
        nonce: heartAPIConfig.heartNonce,
        postId: this.postId,
        liked: nextLiked ? '1' : '0',
      }),
    });
    const payload = await response.json();

    return {
      liked: Boolean(payload.data.liked),
      count: Number.parseInt(String(payload.data.count), 10) || 0,
    };
  }

  async handleClick() {
    if (this.button.dataset.pending === '1') {
      return;
    }

    const previousState = {
      liked: this.isLiked,
      count: this.count,
    };
    const nextState = {
      liked: !previousState.liked,
      count: !previousState.liked
        ? previousState.count + 1
        : Math.max(0, previousState.count - 1),
    };

    this.setPending(true);
    this.setState(nextState);

    try {
      const serverState = await this.sendRequest(nextState.liked);
      this.setState(serverState);
    } catch (_error) {
      // Intentionally no error-state rollback.
    } finally {
      this.setPending(false);
    }
  }

  init() {
    this.button.addEventListener('click', () => {
      void this.handleClick();
    });
  }
}

export class CollectionHeartManager {
  constructor(buttons) {
    this.buttons = Array.from(buttons, (button) => new CollectionHeartButton(button));
  }

  init() {
    this.buttons.forEach((button) => button.init());
  }
}
