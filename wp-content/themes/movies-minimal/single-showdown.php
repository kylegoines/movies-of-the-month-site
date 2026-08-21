<?php
get_header();
?>

<?php get_template_part('header/site', 'header'); ?>

<main class="mx-auto mt-10 min-h-[calc(100vh-114px)] md:max-w-[720px] lg:max-w-[1000px] px-[32px] pb-[50px] sm:min-h-[calc(100vh-154px)] sm:pb-[80px] md:mt-12 lg:min-h-[calc(100vh-232px)] lg:pb-[100px]">
  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <?php
      $showdown_id = get_the_ID();
      $progress_title = movies_theme_get_showdown_progress_title($showdown_id);
      $twitter_url = movies_theme_get_showdown_twitter_url($showdown_id);
      $bluesky_url = movies_theme_get_showdown_bluesky_url($showdown_id);
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
      </article>
    <?php endwhile; ?>
  <?php endif; ?>
</main>

<?php
get_footer();
?>
