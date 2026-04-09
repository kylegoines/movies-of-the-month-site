<?php

$movie_id = $args['movie_id'] ?? 0;
$movie_author_id = $args['movie_author_id'] ?? 0;
$movie_author_name = $args['movie_author_name'] ?? '';
$year = $args['year'] ?? '';
$runtime = $args['runtime'] ?? '';
$genre = $args['genre'] ?? '';
$feature_title = $args['feature_title'] ?? '';
$feature_byline_label = $args['feature_byline_label'] ?? 'Thoughts by';
$featured_content = $args['featured_content'] ?? '';
$pullquote_1 = $args['pullquote_1'] ?? '';
$pullquote_2 = $args['pullquote_2'] ?? '';
$pullquote_3 = $args['pullquote_3'] ?? '';
$pullquote_position_1 = $args['pullquote_position_1'] ?? 'position_1';
$pullquote_position_2 = $args['pullquote_position_2'] ?? 'position_2';
$pullquote_position_3 = $args['pullquote_position_3'] ?? 'position_3';
$is_hidden_gem = $args['is_hidden_gem'] ?? false;
$heart_count = $args['heart_count'] ?? 0;
$is_liked = $args['is_liked'] ?? false;
$funny_rating = $args['funny_rating'] ?? '';
$scary_rating = $args['scary_rating'] ?? '';
$sadness_rating = $args['sadness_rating'] ?? '';
$pacing_rating = $args['pacing_rating'] ?? '';
$has_ratings = $args['has_ratings'] ?? false;
$gem_badge = $args['gem_badge'] ?? '';
$spotlight_image = $args['spotlight_image'] ?? '';

$pullquotes = array_values(array_filter([
    [
        'content' => $pullquote_1,
        'position' => $pullquote_position_1,
    ],
    [
        'content' => $pullquote_2,
        'position' => $pullquote_position_2,
    ],
    [
        'content' => $pullquote_3,
        'position' => $pullquote_position_3,
    ],
], static function (array $pullquote): bool {
    return $pullquote['content'] !== '';
}));

$outer_pullquotes = array_values(array_filter($pullquotes, static function (array $pullquote): bool {
    return $pullquote['position'] !== 'position_2';
}));

$content_pullquotes = array_values(array_filter($pullquotes, static function (array $pullquote): bool {
    return $pullquote['position'] === 'position_2';
}));
?>

<div class="page-header space-y-6">
  <div class="relative">
    <?php foreach ($outer_pullquotes as $pullquote) : ?>
      <div
        class="single-movie-featured__pullquote single-movie-featured__pullquote--<?php echo esc_attr($pullquote['position']); ?> theme-strong"
        data-featured-pullquote
      >
        <?php echo apply_filters('the_content', $pullquote['content']); ?>
      </div>
    <?php endforeach; ?>
    <?php if ($spotlight_image !== '') : ?>
      <?php echo $spotlight_image; ?>
    <?php endif; ?>
  </div>

  <div class="relative z-[2] max-w-[720px] lg:p-6 lg:-mt-[100px] lg:ml-auto" style="background-color: var(--color-background);">
    <?php if ($is_hidden_gem) : ?>
      <p class="movie-title--hidden-gem mb-3 flex items-center gap-2 text-sm font-bold uppercase tracking-[0.08em]">
        <?php echo movies_theme_get_hidden_gem_label_markup(); ?>
        <?php if ($gem_badge !== '') : ?>
          <span class="movie-title__gem"><?php echo $gem_badge; ?></span>
        <?php endif; ?>
      </p>
    <?php endif; ?>

    <div class="mb-6 grid gap-4 lg:items-start">
      <div class="post-content">
        <?php foreach ($content_pullquotes as $pullquote) : ?>
          <div class="single-movie-featured__pullquote-inline theme-strong" data-featured-pullquote>
            <?php echo apply_filters('the_content', $pullquote['content']); ?>
          </div>
        <?php endforeach; ?>
        <?php if ($feature_title !== '') : ?>
          <h1 class="page-header__title theme-strong rhythm-sm"><?php echo esc_html($feature_title); ?></h1>
        <?php endif; ?>
        <p class="theme-body text-sm font-bold tracking-[0.04em] lg:pt-1 rhythm-lg">
          <?php echo esc_html($feature_byline_label); ?>
          <a
            class="theme-strong transition-opacity hover:opacity-70 no-underline"
            href="<?php echo esc_url(get_author_posts_url($movie_author_id)); ?>"
          >
            <?php echo esc_html($movie_author_name); ?>
          </a>
        </p>
        <?php echo apply_filters('the_content', $featured_content); ?>
      </div>
    </div>

    <div class="movie-card theme-body grid max-w-[720px] gap-1.5 text-sm md:text-base mt-[40px] border-4 p-4">
        <h2 class="page-header__title theme-strong rhythm-md">
            <?php the_title(); ?>
        </h2>
      <p>
        <span class="theme-strong font-bold">Recommended by:</span>
        <a
          class="theme-strong transition-opacity hover:opacity-70 no-underline"
          href="<?php echo esc_url(get_author_posts_url($movie_author_id)); ?>"
        >
          <?php echo esc_html($movie_author_name); ?>
        </a>
      </p>

      <?php if ($year !== '') : ?>
        <p><span class="theme-strong font-bold">Release Year:</span> <?php echo esc_html($year); ?></p>
      <?php endif; ?>

      <?php if ($runtime !== '') : ?>
        <p><span class="theme-strong font-bold">Runtime:</span> <?php echo esc_html($runtime); ?></p>
      <?php endif; ?>

      <?php if ($genre !== '') : ?>
        <p><span class="theme-strong font-bold">Genres:</span> <?php echo esc_html($genre); ?></p>
      <?php endif; ?>

      <?php if ($has_ratings) : ?>
        <div class="mt-3 space-y-1.5">
          <p class="theme-strong font-bold">Ratings</p>
          <?php if ($funny_rating !== '') : ?>
            <p><span class="theme-strong font-bold">Funny:</span> <?php echo esc_html($funny_rating); ?></p>
          <?php endif; ?>
          <?php if ($scary_rating !== '') : ?>
            <p><span class="theme-strong font-bold">Scary:</span> <?php echo esc_html($scary_rating); ?></p>
          <?php endif; ?>
          <?php if ($sadness_rating !== '') : ?>
            <p><span class="theme-strong font-bold">Sadness:</span> <?php echo esc_html($sadness_rating); ?></p>
          <?php endif; ?>
          <?php if ($pacing_rating !== '') : ?>
            <p><span class="theme-strong font-bold">Pacing:</span> <?php echo esc_html($pacing_rating); ?></p>
          <?php endif; ?>
        </div>
      <?php endif; ?>


          <div class="collection-heart mt-7">
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
    </div>


    

  </div>
</div>
