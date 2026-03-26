<?php
get_header();
?>

<main class="mx-auto max-w-[1000px] px-6 py-16 md:px-8 md:py-24">
  <?php get_template_part('header/site', 'header'); ?>

  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <?php
      $movie_id = get_the_ID();
      $subtitle = movies_theme_get_subtitle($movie_id);
      $heart_count = movies_theme_get_movie_heart_count($movie_id);
      $is_liked = movies_theme_movie_is_liked_by_current_visitor($movie_id);
      ?>
      <article>
        <header class="mt-[48px]">
          <h1 class="theme-strong text-4xl tracking-[-0.06em] md:text-6xl">
            <?php the_title(); ?>
          </h1>
          <?php if ($subtitle !== '') : ?>
            <p class="theme-body mt-3 text-lg font-bold md:text-xl">
              <?php echo esc_html($subtitle); ?>
            </p>
          <?php endif; ?>

          <div class="collection-heart mt-6">
            <button
              class="collection-heart__button"
              type="button"
              data-heart-button
              data-post-id="<?php echo esc_attr((string) $movie_id); ?>"
              aria-pressed="<?php echo $is_liked ? 'true' : 'false'; ?>"
            >
              <span class="collection-heart__icon" aria-hidden="true"><?php echo $is_liked ? '♥' : '♡'; ?></span>
              <span class="collection-heart__label">Recommend</span>
              <span class="collection-heart__count" data-heart-count><?php echo esc_html((string) $heart_count); ?></span>
            </button>
          </div>
        </header>

        <div class="post-content max-w-[720px]">
          <?php the_content(); ?>
        </div>
      </article>
    <?php endwhile; ?>
  <?php endif; ?>
</main>

<?php
get_footer();
?>
