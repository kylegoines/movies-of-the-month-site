<?php
$showdown = $args['showdown'] ?? null;

if (!$showdown instanceof WP_Post) {
    return;
}

$showdown_id = $showdown->ID;
$progress_title = movies_theme_get_showdown_progress_title($showdown_id);
$twitter_url = movies_theme_get_showdown_twitter_url($showdown_id);
$bluesky_url = movies_theme_get_showdown_bluesky_url($showdown_id);
?>

<aside class="showdown-module theme-accent flex min-h-[300px] flex-col overflow-hidden border-3 border pt-0 px-0 pb-0">
  <div class="spotlight-marquee px-0">
    <div class="spotlight-marquee__track">
      <span>Showdown</span>
      <span class="spotlight-marquee__dot" aria-hidden="true"></span>
      <span>Showdown</span>
      <span class="spotlight-marquee__dot" aria-hidden="true"></span>
      <span>Showdown</span>
      <span class="spotlight-marquee__dot" aria-hidden="true"></span>
      <span>Showdown</span>
      <span class="spotlight-marquee__dot" aria-hidden="true"></span>
      <span>Showdown</span>
      <span class="spotlight-marquee__dot" aria-hidden="true"></span>
      <span>Showdown</span>
      <span class="spotlight-marquee__dot" aria-hidden="true"></span>
    </div>
  </div>

  <div class="theme-body flex flex-1 flex-col px-5 py-6 text-center lg:px-7">
    <div class="mx-auto flex max-w-[240px] flex-1 flex-col items-center justify-center gap-5">
      <div class="theme-strong w-full border-y-3 border-current py-4">
        <h2 class="text-4xl font-black uppercase leading-[0.92] tracking-wide [text-wrap:balance]">
          <?php echo esc_html(get_the_title($showdown_id)); ?>
        </h2>
      </div>

      <?php if ($progress_title !== '') : ?>
        <p class="theme-strong text-sm font-black uppercase leading-snug tracking-[0.22em]">
          <?php echo esc_html($progress_title); ?>
        </p>
      <?php endif; ?>

      <div class="theme-strong h-[3px] w-10 bg-current" aria-hidden="true"></div>
    </div>

    <?php if ($twitter_url !== '' || $bluesky_url !== '') : ?>
      <div class="showdown-module__vote-stub mt-auto flex flex-col items-center gap-4 pt-5 text-sm font-bold uppercase tracking-wide">
        <p class="theme-strong flex items-center gap-2">
          <span aria-hidden="true">&#10022;</span>
          <span>Cast Your Vote</span>
          <span aria-hidden="true">&#10022;</span>
        </p>

        <div class="flex w-full flex-wrap items-center justify-between gap-3">
          <?php if ($twitter_url !== '') : ?>
            <a class="showdown-module__social-link showdown-module__social-link--twitter" href="<?php echo esc_url($twitter_url); ?>" rel="noopener">
              <span>Twitter/X</span>
            </a>
          <?php endif; ?>

          <?php if ($bluesky_url !== '') : ?>
            <a class="showdown-module__social-link showdown-module__social-link--bluesky" href="<?php echo esc_url($bluesky_url); ?>" rel="noopener">
              <span>Bluesky</span>
            </a>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <div class="spotlight-marquee px-0">
    <div class="spotlight-marquee__track">
      <span>Showdown</span>
      <span class="spotlight-marquee__dot" aria-hidden="true"></span>
      <span>Showdown</span>
      <span class="spotlight-marquee__dot" aria-hidden="true"></span>
      <span>Showdown</span>
      <span class="spotlight-marquee__dot" aria-hidden="true"></span>
      <span>Showdown</span>
      <span class="spotlight-marquee__dot" aria-hidden="true"></span>
      <span>Showdown</span>
      <span class="spotlight-marquee__dot" aria-hidden="true"></span>
      <span>Showdown</span>
      <span class="spotlight-marquee__dot" aria-hidden="true"></span>
    </div>
  </div>
</aside>
