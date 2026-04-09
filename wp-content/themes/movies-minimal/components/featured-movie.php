<?php
$featured_movie = $args['featured_movie'] ?? null;

if (!$featured_movie instanceof WP_Post) {
    return;
}

$featured_movie_id = $featured_movie->ID;
$featured_year = movies_theme_get_year($featured_movie_id);
$featured_content = movies_theme_get_featured_content($featured_movie_id);
$featured_image_id = movies_theme_get_featured_image_id($featured_movie_id);
$featured_is_hidden_gem = movies_theme_is_hidden_gem($featured_movie_id);
$featured_gem_badge = $featured_is_hidden_gem
    ? movies_theme_get_inline_svg('images/gem.svg', 'theme-gem-badge')
    : '';
$featured_poster = $featured_image_id > 0
    ? wp_get_attachment_image($featured_image_id, 'large', false, [
        'class' => 'h-auto w-full object-cover',
        'loading' => 'lazy',
    ])
    : get_the_post_thumbnail($featured_movie_id, 'large', [
        'class' => movies_theme_get_poster_image_class($featured_movie_id, 'h-auto w-full object-cover'),
        'loading' => 'lazy',
    ]);
?>

<section class="mt-[96px]">
  <article class="">
    <div>
    <a class="block no-underline" href="<?php echo esc_url(get_permalink($featured_movie_id)); ?>">
        <div class="flex relative">
        <h2 class="theme-strong text-3xl tracking-[-0.05em] lg:text-5xl shrink-0">
          <span class="opacity-90">Spotlight:</span> <?php echo esc_html(get_the_title($featured_movie_id)); ?>
        </h2>
        <div class="accent-rule mt-auto h-[3px] w-[47px] bottom-[-7px] w-full ml-7"></div>
        </div>
        
    </a>
      <?php if ($featured_poster !== '') : ?>
        <a class="block no-underline mb-6" href="<?php echo esc_url(get_permalink($featured_movie_id)); ?>">
          <div class="relative <?php echo $featured_is_hidden_gem ? 'theme-surface' : ''; ?>">
            <?php if ($featured_gem_badge !== '') : ?>
              <span class="poster-frame__badge poster-frame__badge--featured"><?php echo $featured_gem_badge; ?></span>
            <?php endif; ?>
            <?php echo $featured_poster; ?>
          </div>
        </a>
      <?php endif; ?>
    </div>

    <div class="">
      

      <?php if ($featured_content !== '') : ?>
        <div class="post-content mb-5 ">
          <?php echo apply_filters('the_content', $featured_content); ?>
        </div>
      <?php endif; ?>
    </div>
  </article>
</section>
