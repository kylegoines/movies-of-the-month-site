<?php
get_header();
?>

<?php get_template_part('header/site', 'header'); ?>

<main class="mx-auto max-w-[1000px] px-[32px]">
  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <?php
      $movie_id = get_the_ID();
      $movie_author_id = (int) get_post_field('post_author', $movie_id);
      $movie_author_name = movies_theme_get_author_name($movie_author_id);
      $year = movies_theme_get_year($movie_id);
      $runtime = movies_theme_get_runtime($movie_id);
      $genre = movies_theme_get_movie_category_list($movie_id);
      $spoiler_free_review = movies_theme_get_spoiler_free_review($movie_id);
      $featured_content = movies_theme_get_featured_content($movie_id);
      $featured_image_id = movies_theme_get_featured_image_id($movie_id);
      $is_hidden_gem = movies_theme_is_hidden_gem($movie_id);
      $heart_count = movies_theme_get_movie_heart_count($movie_id);
      $is_liked = movies_theme_movie_is_liked_by_current_visitor($movie_id);
      $funny_rating = movies_theme_get_movie_scale_value_label($movie_id, 'funny');
      $scary_rating = movies_theme_get_movie_scale_value_label($movie_id, 'scary');
      $sadness_rating = movies_theme_get_movie_scale_value_label($movie_id, 'sadness');
      $pacing_rating = movies_theme_get_movie_scale_value_label($movie_id, 'pacing');
      $has_ratings = $funny_rating !== '' || $scary_rating !== '' || $sadness_rating !== '' || $pacing_rating !== '';
      $gem_badge = $is_hidden_gem
          ? movies_theme_get_inline_svg('images/gemsingle.svg', 'theme-gem-badge')
          : '';
      $poster = get_the_post_thumbnail($movie_id, 'large', [
          'class' => 'h-auto w-full object-cover',
          'loading' => 'eager',
      ]);
      $has_spotlight_layout = $featured_content !== '';
      $spotlight_image = $featured_image_id > 0
          ? wp_get_attachment_image($featured_image_id, 'large', false, [
              'class' => 'h-auto w-full object-cover',
              'loading' => 'eager',
          ])
          : $poster;
      $other_movies_by_author = get_posts([
          'post_type' => 'movies',
          'post_status' => 'publish',
          'author' => $movie_author_id,
          'post__not_in' => [$movie_id],
          'posts_per_page' => 4,
          'orderby' => 'date',
          'order' => 'DESC',
          'no_found_rows' => true,
      ]);
      ?>
      <article>
        <div class="page-header <?php echo $has_spotlight_layout ? 'space-y-6' : 'grid gap-6 lg:grid-cols-[280px_minmax(0,1fr)] lg:items-start lg:border-3 lg:p-3'; ?>">
          <div class="relative">
            <?php if ($has_spotlight_layout && $spotlight_image !== '') : ?>
              <?php echo $spotlight_image; ?>
            <?php elseif ($poster !== '') : ?>
              <div class="w-full max-w-[220px] lg:w-[280px] lg:max-w-[280px]">
                <?php echo $poster; ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="<?php echo $has_spotlight_layout ? 'relative z-[2] max-w-[720px] p-6 lg:-mt-[100px] lg:ml-auto' : 'relative max-w-[720px] lg:min-h-[420px]'; ?>"<?php echo $has_spotlight_layout ? ' style="background-color: var(--color-background);"' : ''; ?>>
            <?php if ($is_hidden_gem) : ?>
              <p class="movie-title--hidden-gem mb-3 flex items-center gap-2 text-sm font-bold uppercase tracking-[0.08em]">
                <?php echo movies_theme_get_hidden_gem_label_markup(); ?>
                <?php if ($gem_badge !== '') : ?>
                  <span class="movie-title__gem"><?php echo $gem_badge; ?></span>
                <?php endif; ?>
              </p>
            <?php endif; ?>

            <h1 class="page-header__title theme-strong">
              <?php the_title(); ?>
            </h1>

            <div class="theme-body mt-6 grid max-w-[720px] gap-1.5 text-sm md:text-base">
              <p class="order-6 lg:order-1">
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
                <p class="order-4 mt-3 leading-7 lg:order-5">
                  <span class="theme-strong font-bold">Spoiler-Free Review</span><br>
                  <?php echo esc_html($spoiler_free_review); ?>
                </p>
              <?php endif; ?>

              <?php if ($has_ratings) : ?>
                <div class="order-5 mt-4 space-y-1.5 lg:order-6">
                  <p class="theme-strong font-bold">Ratings:</p>
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

              <div class="collection-heart order-6 mt-4 lg:hidden">
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

            <div class="collection-heart absolute right-0 bottom-0 hidden lg:block">
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

            <div class="post-content mt-6">
              <?php
              if ($has_spotlight_layout) {
                  echo apply_filters('the_content', $featured_content);
              } else {
                  the_content();
              }
              ?>
            </div>

            <?php if ($has_spotlight_layout) : ?>
              <p class="theme-body mt-6 text-right text-sm font-bold tracking-[0.04em]">
                &mdash;
                <a
                  class="theme-strong transition-opacity hover:opacity-70 no-underline"
                  href="<?php echo esc_url(get_author_posts_url($movie_author_id)); ?>"
                >
                  <?php echo esc_html($movie_author_name); ?>
                </a>
              </p>
            <?php endif; ?>
          </div>
        </div>

        <?php if ($other_movies_by_author !== []) : ?>
          <section class="rhythm-xl">
            <div class="mb-8 flex items-end gap-6">
              <h2 class="theme-strong text-2xl tracking-[-0.04em] md:text-4xl">
                More from <?php echo esc_html($movie_author_name); ?>
              </h2>
              <div class="accent-rule mb-2 h-[3px] flex-1"></div>
            </div>

            <?php
            get_template_part('components/movie-grid-full', null, [
                'movies' => $other_movies_by_author,
                'grid_classes' => 'grid grid-cols-2 gap-x-6 gap-y-10 sm:grid-cols-3 lg:grid-cols-4',
            ]);
            ?>
          </section>
        <?php endif; ?>
      </article>
    <?php endwhile; ?>
  <?php endif; ?>
</main>

<?php
get_footer();
?>
