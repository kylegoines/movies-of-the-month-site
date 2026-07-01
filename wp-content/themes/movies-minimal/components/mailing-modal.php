<?php
$mailing_signup_status = isset($_GET['mailing_signup_status'])
    ? sanitize_key(wp_unslash($_GET['mailing_signup_status']))
    : '';
$current_request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '/';
$mailing_redirect_to = home_url($current_request_uri);
?>

<div
  class="spotlight-mailing-modal-overlay"
  data-spotlight-mailing-modal-overlay
  data-state="closed"
  aria-hidden="true"
></div>

<div
  id="spotlight-mailing-modal"
  class="spotlight-mailing-modal"
  data-spotlight-mailing-modal
  data-state="closed"
  aria-hidden="true"
  aria-labelledby="spotlight-mailing-modal-title"
  role="dialog"
  aria-modal="true"
>
  <div class="spotlight-mailing-modal__panel">
    <button
      class="spotlight-mailing-modal__close"
      type="button"
      data-spotlight-mailing-modal-close
      aria-label="Close mailing list modal"
    >
      Close
    </button>

    <div class="spotlight-mailing-modal__content">
      <h2 id="spotlight-mailing-modal-title" class="spotlight-mailing-modal__title">
        Lets get you subscribed.
      </h2>
      <div class="spotlight-mailing-modal__body">
        <?php if ($mailing_signup_status === 'success') : ?>
          <p class="spotlight-mailing-modal__notice spotlight-mailing-modal__notice--success">
            You&rsquo;re on the list. We&rsquo;ll keep you posted.
          </p>
        <?php elseif ($mailing_signup_status === 'exists') : ?>
          <p class="spotlight-mailing-modal__notice spotlight-mailing-modal__notice--success">
            That email is already on the list.
          </p>
        <?php elseif ($mailing_signup_status === 'invalid' || $mailing_signup_status === 'error') : ?>
          <p class="spotlight-mailing-modal__notice spotlight-mailing-modal__notice--error">
            Something went wrong. Double-check the email and try again.
          </p>
        <?php endif; ?>

        <p>
          We&rsquo;re still in our infancy, but we&rsquo;d love to keep you posted with fun updates as we grow. We
          promise not to spam you, and we&rsquo;ll never sell your data.
        </p>
        <form class="spotlight-mailing-modal__form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
          <input type="hidden" name="action" value="movies_theme_mailing_signup">
          <input type="hidden" name="redirect_to" value="<?php echo esc_url($mailing_redirect_to); ?>">
          <?php wp_nonce_field('movies_theme_mailing_signup', 'movies_theme_mailing_signup_nonce'); ?>
          <input class="hidden" type="text" name="company" tabindex="-1" autocomplete="off" aria-hidden="true">
          <label class="spotlight-mailing-modal__label" for="spotlight-mailing-email">
            Email Address
          </label>
          <input
            id="spotlight-mailing-email"
            class="spotlight-mailing-modal__input"
            type="email"
            name="email"
            placeholder="you@example.com"
            autocomplete="email"
          >
          <button class="spotlight-mailing-modal__submit" type="submit">
            Subscribe
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
