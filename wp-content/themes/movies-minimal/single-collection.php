<?php
get_header();
?>

<?php get_template_part('header/site', 'header'); ?>

<main class="mx-auto mt-10 min-h-[calc(100vh-114px)] md:max-w-[720px] lg:max-w-[1000px] px-[32px] pb-[50px] sm:min-h-[calc(100vh-154px)] sm:pb-[80px] md:mt-12 lg:min-h-[calc(100vh-232px)] lg:pb-[100px]">
  <?php if (have_posts()) : ?>
      <?php while (have_posts()) : the_post(); ?>
      <?php
      $collection_id = get_the_ID();
      $collection_movie_entries = movies_theme_get_collection_movie_entries($collection_id);
      $movie_ids = $collection_movie_entries !== []
          ? array_values(array_map(static fn(array $entry): int => (int) $entry['movie_id'], $collection_movie_entries))
          : movies_theme_get_collection_movies($collection_id);
      $collection_genres = movies_theme_get_collection_genres($collection_id);
      ?>
      <article>
        <header class="page-header max-w-[720px]">
          <h1 class="page-header__title theme-strong rhythm-lg text-5xl xs:text-6xl md:text-8xl lg:text-[90px]">
            <?php the_title(); ?>
          </h1>
        
          <?php if (get_the_content() !== '') : ?>
            <div class="post-content rhythm-lg text-lg leading-8 md:text-xl md:leading-9">
              <?php the_content(); ?>
            </div>
          <?php endif; ?>

          <?php if ($collection_genres !== []) : ?>
            <div class="flex flex-wrap gap-2">
              <?php foreach ($collection_genres as $genre_index => $genre_name) : ?>
                <span class="collection-list__genre-chip collection-list__genre-chip--<?php echo esc_attr((string) (($genre_index % 12) + 1)); ?>">
                  <?php echo esc_html($genre_name); ?>
                </span>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </header>

        <?php if ($movie_ids !== []) : ?>
          <section class="rhythm-lg flex flex-col rhythm-list-lg">
            <?php foreach ($movie_ids as $movie_index => $movie_id) : ?>
              <?php
              $year = movies_theme_get_year($movie_id);
              $runtime = movies_theme_get_runtime($movie_id);
              $genre = movies_theme_get_movie_category_list($movie_id);
              $the_pitch = movies_theme_get_the_pitch($movie_id);
              $collection_custom_excerpt = $collection_movie_entries !== []
                  ? trim((string) ($collection_movie_entries[$movie_index]['custom_excerpt'] ?? ''))
                  : '';
              $collection_author_id = $collection_movie_entries !== []
                  ? (int) ($collection_movie_entries[$movie_index]['author_id'] ?? 0)
                  : '';
              $border_excerpt = $collection_custom_excerpt;
              $is_hidden_gem = movies_theme_is_hidden_gem($movie_id);
              $heart_count = movies_theme_get_movie_heart_count($movie_id);
              $is_liked = movies_theme_movie_is_liked_by_current_visitor($movie_id);
              $gem_badge = $is_hidden_gem
                  ? movies_theme_get_inline_svg('images/gem.svg', 'theme-gem-badge')
                  : '';
              $movie_author_id = (int) get_post_field('post_author', $movie_id);
              $quote_author_id = $collection_author_id > 0 ? $collection_author_id : $movie_author_id;
              $quote_author_name = movies_theme_get_author_name($quote_author_id);
              $movie_author_name = movies_theme_get_author_name($movie_author_id);
              $poster = get_the_post_thumbnail($movie_id, 'large', [
                  'class' => movies_theme_get_poster_image_class($movie_id, 'mx-auto h-auto max-h-[250px] w-auto object-cover object-center lg:max-h-none lg:w-full'),
                  'loading' => 'lazy',
              ]);
              ?>
              <article class="theme-border grid grid-cols-1 items-start gap-4 border-t pt-6 first:border-t-0 first:pt-0 md:grid-cols-[auto_minmax(0,1fr)] md:gap-8 lg:grid-cols-[220px_minmax(0,1fr)] lg:gap-10">
                <div class="flex h-full flex-col">
                  <?php if ($poster !== '') : ?>
                    <a class="movie-card block no-underline" href="<?php echo esc_url(get_permalink($movie_id)); ?>">
                      <div class="poster-frame theme-surface mt-0 ml-0 w-[min(50vw,180px)] max-h-[250px] before:hidden lg:mt-[20px] lg:ml-[20px] lg:w-auto lg:max-h-none lg:before:block <?php echo $is_hidden_gem ? 'poster-frame--hidden-gem' : ''; ?>">
                        <?php if ($gem_badge !== '') : ?>
                          <span class="poster-frame__badge"><?php echo $gem_badge; ?></span>
                        <?php endif; ?>
                        <?php echo $poster; ?>
                      </div>
                    </a>
                  <?php endif; ?>
                </div>

                <div class="max-w-[720px]">
                  <div class="theme-body text-sm lg:text-base">
                    <?php if ($border_excerpt !== '') : ?>
                      <div class="single-collection__excerpt theme-strong relative mt-10 border border-black p-5 text-xl font-bold leading-tight xs:text-2xl md:text-4xl [&_p]:font-bold">
                        <p><?php echo wp_kses_post(nl2br(esc_html($border_excerpt))); ?></p>
                        <a
                          class="theme-strong absolute right-4 -bottom-2 bg-[var(--color-background)] px-2 text-xs font-bold tracking-[0.04em] no-underline transition-colors hover:text-[#d946ef]"
                          href="<?php echo esc_url(get_author_posts_url($quote_author_id)); ?>"
                        >
                          <?php echo esc_html($quote_author_name); ?>
                        </a>
                      </div>
                    <?php endif; ?>

                    <?php if ($is_hidden_gem) : ?>
                      <p class="hidden-gem-label text-sm font-bold tracking-[0.04em]">
                        <?php echo movies_theme_get_hidden_gem_label_markup(); ?>
                      </p>
                    <?php endif; ?>

                    <p>
                      <span class="theme-strong font-bold">Film:</span>
                      <?php echo esc_html(get_the_title($movie_id)); ?>
                    </p>

                    <?php if ($year !== '') : ?>
                      <p>
                        <span class="theme-strong font-bold">Year:</span>
                        <?php echo esc_html($year); ?>
                      </p>
                    <?php endif; ?>

                    <?php if ($runtime !== '') : ?>
                      <p>
                        <span class="theme-strong font-bold">Runtime:</span>
                        <?php echo esc_html($runtime); ?>
                      </p>
                    <?php endif; ?>

                    <?php if ($genre !== '') : ?>
                      <p>
                        <span class="theme-strong font-bold">Genre:</span>
                        <?php echo esc_html($genre); ?>
                      </p>
                    <?php endif; ?>

                    <p>
                        <span class="theme-strong font-bold">Recommended by:</span>
                        <a
                          class="theme-strong transition-opacity hover:opacity-70"
                          href="<?php echo esc_url(get_author_posts_url($movie_author_id)); ?>"
                        >
                        <?php echo esc_html($movie_author_name); ?>
                        </a>
                    </p>

                    <?php if ($the_pitch !== '') : ?>
                      <p>
                        <span class="theme-strong font-bold">The Pitch:</span>
                        <?php echo wp_kses_post(nl2br(esc_html($the_pitch))); ?>
                      </p>
                    <?php endif; ?>

                    <div class="collection-heart mt-5">
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
              </article>
            <?php endforeach; ?>
          </section>
        <?php endif; ?>
      </article>
    <?php endwhile; ?>
  <?php endif; ?>
</main>

<?php
get_footer();
?>
