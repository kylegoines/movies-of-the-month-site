<?php
$showdown = $args['showdown'] ?? null;

if (!$showdown instanceof WP_Post) {
    return;
}

$showdown_id = $showdown->ID;
$showdown_mode = movies_theme_get_showdown_mode($showdown_id);
$progress_title = movies_theme_get_showdown_progress_title($showdown_id);
$twitter_url = movies_theme_get_showdown_twitter_url($showdown_id);
$bluesky_url = movies_theme_get_showdown_bluesky_url($showdown_id);
$finals_label = movies_theme_get_showdown_finals_label($showdown_id);
$finals_poster_ids = movies_theme_get_showdown_finals_poster_ids($showdown_id);
$winner_data = movies_theme_get_showdown_winner_data($showdown_id);
$winner_poster = '';

if ($showdown_mode === 'winner' && $winner_data['poster_id'] > 0) {
    $winner_poster_class = 'h-full w-full object-cover object-center';

    if ($winner_data['movie_id'] > 0) {
        $winner_poster_class = movies_theme_get_poster_image_class(
            $winner_data['movie_id'],
            $winner_poster_class
        );
    }

    $winner_poster = wp_get_attachment_image($winner_data['poster_id'], 'large', false, [
        'class' => $winner_poster_class,
        'loading' => 'lazy',
    ]);
}
?>

<aside class="showdown-module showdown-module--<?php echo esc_attr($showdown_mode); ?> theme-accent flex min-h-[300px] flex-col overflow-hidden border-3 border pt-0 px-0 pb-0">
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
    <?php if ($showdown_mode === 'finals') : ?>
      <div class="mx-auto flex w-full flex-col items-center gap-5">
        <div class="theme-strong w-full max-w-[240px] border-y-3 border-current py-4">
          <h2 class="text-4xl font-black uppercase leading-[0.92] tracking-wide [text-wrap:balance]">
            <?php echo esc_html(get_the_title($showdown_id)); ?>
          </h2>
        </div>

        <p class="theme-strong flex items-center gap-2 text-sm font-black uppercase tracking-[0.22em]">
          <span aria-hidden="true">&#10022;</span>
          <span><?php echo esc_html($finals_label); ?></span>
          <span aria-hidden="true">&#10022;</span>
        </p>

        <div class="-mx-5 w-[calc(100%+2.5rem)] lg:-mx-7 lg:w-[calc(100%+3.5rem)]">
          <?php get_template_part('components/showdown-finals', 'row', [
              'poster_ids' => $finals_poster_ids,
          ]); ?>
        </div>

        <?php if ($twitter_url !== '' || $bluesky_url !== '') : ?>
          <div class="showdown-module__vote-stub flex w-full flex-col items-center gap-4 pt-1 text-sm font-bold uppercase tracking-wide">
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
    <?php elseif ($showdown_mode === 'winner') : ?>
      <div class="mx-auto flex w-full max-w-[240px] flex-col items-center gap-5">
        <div class="theme-strong w-full border-y-3 border-current py-4">
          <h2 class="text-4xl font-black uppercase leading-[0.92] tracking-wide [text-wrap:balance]">
            <?php echo esc_html(get_the_title($showdown_id)); ?>
          </h2>
        </div>

        <p class="theme-strong flex items-center gap-2 text-sm font-black uppercase tracking-[0.22em]">
          <span aria-hidden="true">&#10022;</span>
          <span><?php echo esc_html($winner_data['label']); ?></span>
          <span aria-hidden="true">&#10022;</span>
        </p>

        <?php if ($winner_poster !== '') : ?>
          <?php if ($winner_data['url'] !== '') : ?>
            <a class="showdown-module__winner-poster block w-full overflow-hidden border-3 border-current no-underline transition-opacity hover:opacity-80" href="<?php echo esc_url($winner_data['url']); ?>" rel="noopener">
              <span class="block aspect-[2/3]">
                <?php echo $winner_poster; ?>
              </span>
            </a>
          <?php else : ?>
            <div class="showdown-module__winner-poster w-full overflow-hidden border-3 border-current">
              <div class="aspect-[2/3]">
                <?php echo $winner_poster; ?>
              </div>
            </div>
          <?php endif; ?>
        <?php endif; ?>

        <?php if ($winner_data['title'] !== '') : ?>
          <?php if ($winner_data['url'] !== '') : ?>
            <a class="theme-strong no-underline transition-opacity hover:opacity-70" href="<?php echo esc_url($winner_data['url']); ?>" rel="noopener">
              <h3 class="text-xl font-black uppercase leading-tight tracking-wide [text-wrap:balance]">
                <?php echo esc_html($winner_data['title']); ?>
              </h3>
            </a>
          <?php else : ?>
            <h3 class="theme-strong text-xl font-black uppercase leading-tight tracking-wide [text-wrap:balance]">
              <?php echo esc_html($winner_data['title']); ?>
            </h3>
          <?php endif; ?>
        <?php endif; ?>

        <?php if ($twitter_url !== '' || $bluesky_url !== '') : ?>
          <div class="flex w-full flex-wrap items-center justify-between gap-3 text-sm font-bold uppercase tracking-wide">
            <?php if ($twitter_url !== '') : ?>
              <a class="showdown-module__social-link showdown-module__social-link--twitter" href="<?php echo esc_url($twitter_url); ?>" rel="noopener" aria-label="View results on Twitter/X">
                <span>Results</span>
                <?php echo movies_theme_get_inline_svg('images/twitter.svg', 'showdown-results-link__icon'); ?>
              </a>
            <?php endif; ?>

            <?php if ($bluesky_url !== '') : ?>
              <a class="showdown-module__social-link showdown-module__social-link--bluesky" href="<?php echo esc_url($bluesky_url); ?>" rel="noopener" aria-label="View results on Bluesky">
                <span>Results</span>
                <?php echo movies_theme_get_inline_svg('images/bluesky.svg', 'showdown-results-link__icon'); ?>
              </a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php else : ?>
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
