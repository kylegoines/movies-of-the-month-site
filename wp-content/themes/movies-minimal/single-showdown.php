<?php
get_header();
?>

<?php get_template_part('header/site', 'header'); ?>

<main class="mx-auto mt-10 min-h-[calc(100vh-114px)] md:max-w-[720px] lg:max-w-[1000px] px-[32px] pb-[50px] sm:min-h-[calc(100vh-154px)] sm:pb-[80px] md:mt-12 lg:min-h-[calc(100vh-232px)] lg:pb-[100px]">
  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <?php
      $showdown_id = get_the_ID();
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

      <article class="mx-auto max-w-[720px]">
        <div class="spotlight-marquee mb-10 px-0">
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

        <h1 class="theme-strong mb-4 text-5xl font-bold leading-none md:text-7xl">
          <?php the_title(); ?>
        </h1>

        <?php if ($showdown_mode === 'finals') : ?>
          <div class="mt-8 flex flex-col items-start gap-6">
            <p class="theme-strong flex items-center gap-2 text-sm font-black uppercase tracking-[0.22em]">
              <span aria-hidden="true">&#10022;</span>
              <span><?php echo esc_html($finals_label); ?></span>
              <span aria-hidden="true">&#10022;</span>
            </p>

            <?php get_template_part('components/showdown-finals', 'row', [
                'poster_ids' => $finals_poster_ids,
            ]); ?>

            <?php if ($twitter_url !== '' || $bluesky_url !== '') : ?>
              <div class="flex flex-wrap gap-4 text-base font-bold">
                <?php if ($twitter_url !== '') : ?>
                  <a class="underline decoration-2 underline-offset-4 transition-opacity hover:opacity-70" href="<?php echo esc_url($twitter_url); ?>" rel="noopener">
                    Twitter/X
                  </a>
                <?php endif; ?>

                <?php if ($bluesky_url !== '') : ?>
                  <a class="underline decoration-2 underline-offset-4 transition-opacity hover:opacity-70" href="<?php echo esc_url($bluesky_url); ?>" rel="noopener">
                    Bluesky
                  </a>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
        <?php elseif ($showdown_mode === 'winner') : ?>
          <div class="mt-8 flex flex-col items-start gap-6">
            <p class="theme-strong flex items-center gap-2 text-sm font-black uppercase tracking-[0.22em]">
              <span aria-hidden="true">&#10022;</span>
              <span><?php echo esc_html($winner_data['label']); ?></span>
              <span aria-hidden="true">&#10022;</span>
            </p>

            <?php if ($winner_poster !== '') : ?>
              <?php if ($winner_data['url'] !== '') : ?>
                <a class="block w-full max-w-[320px] overflow-hidden border-3 border-current no-underline transition-opacity hover:opacity-80" href="<?php echo esc_url($winner_data['url']); ?>" rel="noopener">
                  <span class="block aspect-[2/3]">
                    <?php echo $winner_poster; ?>
                  </span>
                </a>
              <?php else : ?>
                <div class="w-full max-w-[320px] overflow-hidden border-3 border-current">
                  <div class="aspect-[2/3]">
                    <?php echo $winner_poster; ?>
                  </div>
                </div>
              <?php endif; ?>
            <?php endif; ?>

            <?php if ($winner_data['title'] !== '') : ?>
              <?php if ($winner_data['url'] !== '') : ?>
                <a class="theme-strong no-underline transition-opacity hover:opacity-70" href="<?php echo esc_url($winner_data['url']); ?>" rel="noopener">
                  <h2 class="text-3xl font-black uppercase leading-tight tracking-wide">
                    <?php echo esc_html($winner_data['title']); ?>
                  </h2>
                </a>
              <?php else : ?>
                <h2 class="theme-strong text-3xl font-black uppercase leading-tight tracking-wide">
                  <?php echo esc_html($winner_data['title']); ?>
                </h2>
              <?php endif; ?>
            <?php endif; ?>

            <?php if ($twitter_url !== '' || $bluesky_url !== '') : ?>
              <div class="flex flex-wrap gap-4 text-base font-bold">
                <?php if ($twitter_url !== '') : ?>
                  <a class="inline-flex items-center gap-2 underline decoration-2 underline-offset-4 transition-opacity hover:opacity-70" href="<?php echo esc_url($twitter_url); ?>" rel="noopener" aria-label="View results on Twitter/X">
                    <span>Results</span>
                    <?php echo movies_theme_get_inline_svg('images/twitter.svg', 'showdown-results-link__icon'); ?>
                  </a>
                <?php endif; ?>

                <?php if ($bluesky_url !== '') : ?>
                  <a class="inline-flex items-center gap-2 underline decoration-2 underline-offset-4 transition-opacity hover:opacity-70" href="<?php echo esc_url($bluesky_url); ?>" rel="noopener" aria-label="View results on Bluesky">
                    <span>Results</span>
                    <?php echo movies_theme_get_inline_svg('images/bluesky.svg', 'showdown-results-link__icon'); ?>
                  </a>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
        <?php else : ?>
          <?php if ($progress_title !== '') : ?>
            <p class="theme-body mb-8 text-xl font-bold leading-snug md:text-2xl">
              <?php echo esc_html($progress_title); ?>
            </p>
          <?php endif; ?>

          <?php if ($twitter_url !== '' || $bluesky_url !== '') : ?>
            <div class="flex flex-wrap gap-4 text-base font-bold">
              <?php if ($twitter_url !== '') : ?>
                <a class="underline decoration-2 underline-offset-4 transition-opacity hover:opacity-70" href="<?php echo esc_url($twitter_url); ?>" rel="noopener">
                  Twitter/X
                </a>
              <?php endif; ?>

              <?php if ($bluesky_url !== '') : ?>
                <a class="underline decoration-2 underline-offset-4 transition-opacity hover:opacity-70" href="<?php echo esc_url($bluesky_url); ?>" rel="noopener">
                  Bluesky
                </a>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </article>
    <?php endwhile; ?>
  <?php endif; ?>
</main>

<?php
get_footer();
?>
