<?php
get_header();
get_template_part('header/site', 'header');
?>

<main class="mx-auto max-w-[1000px] px-6 pt-2 pb-16 md:px-8 md:pt-2 md:pb-24">
  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <?php
      $movie_id = get_the_ID();
      $movie_author_id = (int) get_post_field('post_author', $movie_id);
      $movie_author_name = movies_theme_get_author_name($movie_author_id);
      $subtitle = movies_theme_get_subtitle($movie_id);
      $year = movies_theme_get_year($movie_id);
      $runtime = movies_theme_get_runtime($movie_id);
      $genre = movies_theme_get_movie_category_list($movie_id);
      $brief_synopsis = movies_theme_get_brief_synopsis($movie_id);
      $featured_content = movies_theme_get_featured_content($movie_id);
      $featured_image_id = movies_theme_get_featured_image_id($movie_id);
      $is_hidden_gem = movies_theme_is_hidden_gem($movie_id);
      $heart_count = movies_theme_get_movie_heart_count($movie_id);
      $is_liked = movies_theme_movie_is_liked_by_current_visitor($movie_id);
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
        <div class="mt-[56px] <?php echo $has_spotlight_layout ? 'space-y-10' : 'grid gap-10 lg:grid-cols-[minmax(180px,20vw)_minmax(0,1fr)] lg:items-start'; ?>">
          <div>
            <?php if ($has_spotlight_layout && $spotlight_image !== '') : ?>
              <?php echo $spotlight_image; ?>
            <?php elseif ($poster !== '') : ?>
              <div class="w-full max-w-[220px] lg:max-w-none">
                <?php echo $poster; ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="<?php echo $has_spotlight_layout ? 'relative z-[2] max-w-[720px] p-6 lg:-mt-[100px] lg:ml-auto' : 'max-w-[720px]'; ?>"<?php echo $has_spotlight_layout ? ' style="background-color: var(--color-background);"' : ''; ?>>
            <?php if ($is_hidden_gem) : ?>
              <p class="movie-title--hidden-gem mb-3 flex items-center gap-2 text-sm font-bold uppercase tracking-[0.08em]">
                <span>Hidden Gem</span>
                <?php if ($gem_badge !== '') : ?>
                  <span class="movie-title__gem"><?php echo $gem_badge; ?></span>
                <?php endif; ?>
              </p>
            <?php endif; ?>

            <h1 class="theme-strong text-4xl tracking-[-0.06em] md:text-6xl">
              <?php the_title(); ?>
            </h1>

            <?php if ($subtitle !== '') : ?>
              <p class="theme-body mt-3 text-lg font-bold md:text-xl">
                <?php echo esc_html($subtitle); ?>
              </p>
            <?php endif; ?>

            <div class="theme-body mt-8 grid max-w-[720px] gap-2 text-sm md:text-base">
              <?php if ($year !== '') : ?>
                <p><span class="theme-strong font-bold">Year:</span> <?php echo esc_html($year); ?></p>
              <?php endif; ?>

              <?php if ($runtime !== '') : ?>
                <p><span class="theme-strong font-bold">Runtime:</span> <?php echo esc_html($runtime); ?></p>
              <?php endif; ?>

              <?php if ($genre !== '') : ?>
                <p><span class="theme-strong font-bold">Genre:</span> <?php echo esc_html($genre); ?></p>
              <?php endif; ?>

              <?php if ($brief_synopsis !== '') : ?>
                <p class="leading-7"><span class="theme-strong font-bold">Brief Synopsis:</span> <?php echo esc_html($brief_synopsis); ?></p>
              <?php endif; ?>
            </div>

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

            <div class="post-content mt-8">
              <?php
              if ($has_spotlight_layout) {
                  echo apply_filters('the_content', $featured_content);
              } else {
                  the_content();
              }
              ?>
            </div>

            <p class="theme-body mt-10 text-sm font-bold tracking-[0.04em] <?php echo $has_spotlight_layout ? 'text-right' : ''; ?>">
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

        <?php if ($other_movies_by_author !== []) : ?>
          <section class="mt-28">
            <div class="mb-8 flex items-end gap-6">
              <h2 class="theme-strong text-2xl tracking-[-0.04em] md:text-4xl">
                More from <?php echo esc_html($movie_author_name); ?>
              </h2>
              <div class="accent-rule mb-2 h-[3px] flex-1"></div>
            </div>

            <div class="grid grid-cols-2 gap-x-6 gap-y-10 md:grid-cols-4">
              <?php foreach ($other_movies_by_author as $related_movie) : ?>
                <?php
                $related_movie_id = $related_movie->ID;
                $related_subtitle = movies_theme_get_subtitle($related_movie_id);
                $related_is_hidden_gem = movies_theme_is_hidden_gem($related_movie_id);
                $related_gem_badge = $related_is_hidden_gem
                    ? movies_theme_get_inline_svg('images/gemsingle.svg', 'theme-gem-badge')
                    : '';
                $related_poster = get_the_post_thumbnail($related_movie_id, 'large', [
                    'class' => 'h-auto w-full object-cover',
                    'loading' => 'lazy',
                ]);
                ?>
                <article>
                  <a class="movie-card block no-underline" href="<?php echo esc_url(get_permalink($related_movie_id)); ?>">
                    <?php if ($related_poster !== '') : ?>
                      <div class="poster-frame theme-surface <?php echo $related_is_hidden_gem ? 'poster-frame--hidden-gem' : ''; ?>">
                        <?php echo $related_poster; ?>
                      </div>
                    <?php endif; ?>

                    <h3 class="mt-4 flex items-center gap-2 text-xl tracking-[-0.04em] <?php echo $related_is_hidden_gem ? 'movie-title--hidden-gem' : 'theme-strong'; ?>">
                      <span><?php echo esc_html(get_the_title($related_movie_id)); ?></span>
                      <?php if ($related_gem_badge !== '') : ?>
                        <span class="movie-title__gem"><?php echo $related_gem_badge; ?></span>
                      <?php endif; ?>
                    </h3>

                    <?php if ($related_subtitle !== '') : ?>
                      <p class="theme-body mt-2 text-base font-bold">
                        <?php echo esc_html($related_subtitle); ?>
                      </p>
                    <?php endif; ?>
                  </a>
                </article>
              <?php endforeach; ?>
            </div>
          </section>
        <?php endif; ?>
      </article>
    <?php endwhile; ?>
  <?php endif; ?>
</main>

<?php
get_footer();
?>
