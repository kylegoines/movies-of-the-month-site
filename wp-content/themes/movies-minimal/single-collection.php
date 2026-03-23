<?php
get_header();
?>

<main class="mx-auto max-w-[1000px] px-6 py-16 md:px-8 md:py-24">
  <?php get_template_part('header/site', 'header'); ?>

  <?php if (have_posts()) : ?>
      <?php while (have_posts()) : the_post(); ?>
      <?php
      $collection_id = get_the_ID();
      $movie_ids = movies_theme_get_collection_movies($collection_id);
      $heart_count = movies_theme_get_collection_heart_count($collection_id);
      $is_liked = movies_theme_collection_is_liked_by_current_visitor($collection_id);
      ?>
      <article>
        <header class="mt-[48px] max-w-[720px]">
          <h1 class="theme-strong text-4xl tracking-[-0.06em] md:text-6xl">
            <?php the_title(); ?>
          </h1>

          <div class="collection-heart mt-6">
            <button
              class="collection-heart__button"
              type="button"
              data-heart-button
              data-post-id="<?php echo esc_attr((string) $collection_id); ?>"
              aria-pressed="<?php echo $is_liked ? 'true' : 'false'; ?>"
            >
              <span class="collection-heart__icon" aria-hidden="true"><?php echo $is_liked ? '♥' : '♡'; ?></span>
              <span class="collection-heart__label">Heart</span>
              <span class="collection-heart__count" data-heart-count><?php echo esc_html((string) $heart_count); ?></span>
            </button>
          </div>
        
          <?php if (get_the_content() !== '') : ?>
            <div class="post-content">
              <?php the_content(); ?>
            </div>
          <?php endif; ?>
        </header>

        <?php if ($movie_ids !== []) : ?>
          <section class="mt-12 flex flex-col gap-12 sm:gap-14 md:gap-16">
            <?php foreach ($movie_ids as $movie_id) : ?>
              <?php
              $subtitle = movies_theme_get_subtitle($movie_id);
              $year = movies_theme_get_year($movie_id);
              $runtime = movies_theme_get_runtime($movie_id);
              $genre = movies_theme_get_movie_category_list($movie_id);
              $brief_synopsis = movies_theme_get_brief_synopsis($movie_id);
              $movie_author_id = (int) get_post_field('post_author', $movie_id);
              $poster = get_the_post_thumbnail($movie_id, 'large', [
                  'class' => 'h-auto w-full object-cover',
                  'loading' => 'lazy',
              ]);
              ?>
              <article class="theme-border grid gap-6 border-t pt-6 md:grid-cols-[220px_minmax(0,1fr)] md:gap-10">
                <div>
                  <?php if ($poster !== '') : ?>
                    <div class="poster-frame theme-surface">
                      <?php echo $poster; ?>
                    </div>
                  <?php endif; ?>
                </div>

                <div class="max-w-[720px]">
                  <div class="theme-body text-sm md:text-base">
                    <p>
                      <span class="theme-strong font-bold">Staff member:</span>
                      <a
                        class="theme-strong transition-opacity hover:opacity-70"
                        href="<?php echo esc_url(get_author_posts_url($movie_author_id)); ?>"
                      >
                        <?php echo esc_html(get_the_author_meta('display_name', $movie_author_id)); ?>
                      </a>
                    </p>

                    <p>
                      <span class="theme-strong font-bold">Film:</span>
                      <?php echo esc_html(get_the_title($movie_id)); ?>
                    </p>

                    <?php if ($subtitle !== '') : ?>
                      <p>
                        <span class="theme-strong font-bold">Subtitle:</span>
                        <?php echo esc_html($subtitle); ?>
                      </p>
                    <?php endif; ?>

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

                    <?php if ($brief_synopsis !== '') : ?>
                      <p class="leading-7">
                        <span class="theme-strong font-bold">Brief Synopsis:</span>
                        <?php echo esc_html($brief_synopsis); ?>
                      </p>
                    <?php endif; ?>
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
