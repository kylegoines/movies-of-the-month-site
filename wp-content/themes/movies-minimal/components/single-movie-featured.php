<?php

$movie_id = $args['movie_id'] ?? 0;
$movie_author_id = $args['movie_author_id'] ?? 0;
$movie_author_name = $args['movie_author_name'] ?? '';
$year = $args['year'] ?? '';
$runtime = $args['runtime'] ?? '';
$genre = $args['genre'] ?? '';
$spoiler_free_review = $args['spoiler_free_review'] ?? '';
$featured_content = $args['featured_content'] ?? '';
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
?>

<div class="page-header space-y-6">
  <div class="relative">
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

    <h1 class="page-header__title theme-strong rhythm-lg">
      <?php the_title(); ?>
    </h1>

    <div class="theme-body mb-6 grid max-w-[720px] gap-1.5 text-sm md:text-base">
      <p class="">
        <span class="theme-strong font-bold">Recommended by:</span>
        <a
          class="theme-strong transition-opacity hover:opacity-70 no-underline"
          href="<?php echo esc_url(get_author_posts_url($movie_author_id)); ?>"
        >
          <?php echo esc_html($movie_author_name); ?>
        </a>
      </p>

      <?php if ($year !== '') : ?>
        <p class="order-1 lg:order-2"><span class="theme-strong font-bold">Release Year:</span> <?php echo esc_html($year); ?></p>
      <?php endif; ?>

      <?php if ($runtime !== '') : ?>
        <p class="order-2 lg:order-3"><span class="theme-strong font-bold">Runtime:</span> <?php echo esc_html($runtime); ?></p>
      <?php endif; ?>

      <?php if ($genre !== '') : ?>
        <p class="order-3 lg:order-4"><span class="theme-strong font-bold">Genres:</span> <?php echo esc_html($genre); ?></p>
      <?php endif; ?>

      <?php if ($spoiler_free_review !== '') : ?>
        <p class="order-4 mb-3 leading-7 lg:order-5">
          <span class="theme-strong font-bold">Spoiler-Free Review</span><br>
          <?php echo esc_html($spoiler_free_review); ?>
        </p>
      <?php endif; ?>

      <?php if ($has_ratings) : ?>
        <div class="order-5 mb-4 mt-3 space-y-1.5 lg:order-6">
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
    </div>

    <div class="collection-heart absolute left-[20px] bottom-0 hidden lg:block">
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

    <div class="mb-6 grid gap-4 lg:items-start">
      <div class="post-content">
        <?php echo apply_filters('the_content', $featured_content); ?>
              <p class="theme-body text-sm font-bold tracking-[0.04em] lg:pt-1">
        &mdash;
        <a
          class="theme-strong transition-opacity hover:opacity-70 no-underline"
          href="<?php echo esc_url(get_author_posts_url($movie_author_id)); ?>"
        >
          <?php echo esc_html($movie_author_name); ?>
        </a>
      </p>
      </div>
    </div>
  </div>
</div>
