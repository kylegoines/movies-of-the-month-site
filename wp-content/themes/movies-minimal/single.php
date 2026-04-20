<?php
get_header();
?>

<?php get_template_part('header/site', 'header'); ?>

<main class="mx-auto mt-10 min-h-[calc(100vh-114px)] md:max-w-[720px] lg:max-w-[1000px] px-[32px] pb-[50px] sm:min-h-[calc(100vh-154px)] sm:pb-[80px] md:mt-12 lg:min-h-[calc(100vh-232px)] lg:pb-[100px]">
  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <?php
      $movie_id = get_the_ID();
      $movie_author_id = (int) get_post_field('post_author', $movie_id);
      $movie_author_name = movies_theme_get_author_name($movie_author_id);
      $editorial_author_id = movies_theme_get_editorial_author_id($movie_id);
      $editorial_author_id = $editorial_author_id > 0 ? $editorial_author_id : $movie_author_id;
      $editorial_author_name = movies_theme_get_author_name($editorial_author_id);
      $year = movies_theme_get_year($movie_id);
      $runtime = movies_theme_get_runtime($movie_id);
      $genre = movies_theme_get_movie_category_list($movie_id);
      $feature_title = movies_theme_get_feature_title($movie_id);
      $feature_byline_label = movies_theme_get_feature_byline_label($movie_id);
      $featured_content = movies_theme_get_featured_content($movie_id);
      $pullquote_1 = movies_theme_get_pullquote_1($movie_id);
      $pullquote_2 = movies_theme_get_pullquote_2($movie_id);
      $pullquote_3 = movies_theme_get_pullquote_3($movie_id);
      $pullquote_position_1 = movies_theme_get_pullquote_position($movie_id, 1);
      $pullquote_position_2 = movies_theme_get_pullquote_position($movie_id, 2);
      $pullquote_position_3 = movies_theme_get_pullquote_position($movie_id, 3);
      $featured_image_id = movies_theme_get_featured_image_id($movie_id);
      $related_movies = movies_theme_get_related_movies($movie_id);
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
          'class' => movies_theme_get_poster_image_class($movie_id, 'h-auto w-full object-cover'),
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
        <?php
        get_template_part(
            $has_spotlight_layout ? 'components/single-movie-featured' : 'components/single-movie-standard',
            null,
            [
                'movie_id' => $movie_id,
                'movie_author_id' => $movie_author_id,
                'movie_author_name' => $movie_author_name,
                'editorial_author_id' => $editorial_author_id,
                'editorial_author_name' => $editorial_author_name,
                'year' => $year,
                'runtime' => $runtime,
                'genre' => $genre,
                'feature_title' => $feature_title,
                'feature_byline_label' => $feature_byline_label,
                'featured_content' => $featured_content,
                'pullquote_1' => $pullquote_1,
                'pullquote_2' => $pullquote_2,
                'pullquote_3' => $pullquote_3,
                'pullquote_position_1' => $pullquote_position_1,
                'pullquote_position_2' => $pullquote_position_2,
                'pullquote_position_3' => $pullquote_position_3,
                'is_hidden_gem' => $is_hidden_gem,
                'heart_count' => $heart_count,
                'is_liked' => $is_liked,
                'funny_rating' => $funny_rating,
                'scary_rating' => $scary_rating,
                'sadness_rating' => $sadness_rating,
                'pacing_rating' => $pacing_rating,
                'has_ratings' => $has_ratings,
                'gem_badge' => $gem_badge,
                'poster' => $poster,
                'spotlight_image' => $spotlight_image,
            ]
        );
        ?>

        <?php if (!$has_spotlight_layout && $related_movies !== []) : ?>
          <section class="rhythm-xl">
            <div class="mb-8 flex items-end gap-6">
              <h2 class="theme-strong text-2xl tracking-[-0.04em] md:text-4xl">
                Related Movies
              </h2>
              <div class="accent-rule mb-2 h-[3px] flex-1"></div>
            </div>

            <?php
            get_template_part('components/movie-grid-full', null, [
                'movies' => $related_movies,
                'grid_classes' => 'grid grid-cols-2 gap-x-6 gap-y-10 sm:grid-cols-3 lg:grid-cols-4',
            ]);
            ?>
          </section>
        <?php endif; ?>

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
