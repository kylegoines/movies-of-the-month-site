<?php

$movies = $args['movies'] ?? [];
$grid_classes = $args['grid_classes'] ?? 'grid grid-cols-2 gap-x-6 gap-y-10 sm:grid-cols-3 lg:grid-cols-4';

if (!is_array($movies) || $movies === []) {
    return;
}
?>

<section class="<?php echo esc_attr($grid_classes); ?>">
  <?php foreach ($movies as $movie) : ?>
    <?php
    $movie_id = $movie instanceof WP_Post ? (int) $movie->ID : (int) $movie;

    if ($movie_id <= 0) {
        continue;
    }

    $is_hidden_gem = movies_theme_is_hidden_gem($movie_id);
    $gem_badge = $is_hidden_gem
        ? movies_theme_get_inline_svg('images/gemsingle.svg', 'theme-gem-badge')
        : '';
    $poster = get_the_post_thumbnail($movie_id, 'large', [
        'class' => movies_theme_get_poster_image_class($movie_id, 'h-full w-full object-cover object-center'),
        'loading' => 'lazy',
    ]);
    ?>
    <article>
      <a class="movie-card block no-underline" href="<?php echo esc_url(get_permalink($movie_id)); ?>">
        <?php if ($poster !== '') : ?>
          <div class="poster-frame theme-surface mt-0 ml-0 aspect-[2/3] w-full before:hidden lg:mt-[20px] lg:ml-[20px] lg:before:block <?php echo $is_hidden_gem ? 'poster-frame--hidden-gem' : ''; ?>">
            <div class="h-full w-full overflow-hidden">
              <?php echo $poster; ?>
            </div>
          </div>
        <?php endif; ?>

        <h3 class=" mt-4 flex items-center gap-2 text-xl tracking-[-0.04em] <?php echo $is_hidden_gem ? 'movie-title--hidden-gem' : 'theme-strong'; ?>">
          <span><?php echo esc_html(get_the_title($movie_id)); ?></span>
          <?php if ($gem_badge !== '') : ?>
            <span class="movie-title__gem"><?php echo $gem_badge; ?></span>
          <?php endif; ?>
        </h3>
      </a>
    </article>
  <?php endforeach; ?>
</section>
