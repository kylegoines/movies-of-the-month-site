<?php
get_header();
?>

<main class="mx-auto max-w-[1000px] px-6 py-16 md:px-8 md:py-24">
  <?php get_template_part('header/site', 'header'); ?>

  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <?php $movie_ids = movies_minimal_get_collection_movies(get_the_ID()); ?>
      <article>
        <header class="mt-[48px] max-w-[720px]">
          <h1 class="text-4xl tracking-[-0.06em] text-black md:text-6xl">
            <?php the_title(); ?>
          </h1>
        
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
              $subtitle = movies_minimal_get_subtitle($movie_id);
              $year = movies_minimal_get_year($movie_id);
              $runtime = movies_minimal_get_runtime($movie_id);
              $genre = movies_minimal_get_movie_category_list($movie_id);
              $brief_synopsis = movies_minimal_get_brief_synopsis($movie_id);
              $movie_author_id = (int) get_post_field('post_author', $movie_id);
              $poster = get_the_post_thumbnail($movie_id, 'large', [
                  'class' => 'h-auto w-full object-cover',
                  'loading' => 'lazy',
              ]);
              ?>
              <article class="grid gap-6 border-t border-black pt-6 md:grid-cols-[220px_minmax(0,1fr)] md:gap-10">
                <div>
                  <?php if ($poster !== '') : ?>
                    <div class="overflow-hidden bg-neutral-100">
                      <?php echo $poster; ?>
                    </div>
                  <?php endif; ?>
                </div>

                <div class="max-w-[720px]">
                  <div class="text-sm text-neutral-800 md:text-base">
                    <p>
                      <span class="font-bold">Staff member:</span>
                      <a
                        class="text-black transition-opacity hover:opacity-70"
                        href="<?php echo esc_url(get_author_posts_url($movie_author_id)); ?>"
                      >
                        <?php echo esc_html(get_the_author_meta('display_name', $movie_author_id)); ?>
                      </a>
                    </p>

                    <p>
                      <span class="font-bold text-black">Film:</span>
                      <?php echo esc_html(get_the_title($movie_id)); ?>
                    </p>

                    <?php if ($subtitle !== '') : ?>
                      <p>
                        <span class="font-bold text-black">Subtitle:</span>
                        <?php echo esc_html($subtitle); ?>
                      </p>
                    <?php endif; ?>

                    <?php if ($year !== '') : ?>
                      <p>
                        <span class="font-bold text-black">Year:</span>
                        <?php echo esc_html($year); ?>
                      </p>
                    <?php endif; ?>

                    <?php if ($runtime !== '') : ?>
                      <p>
                        <span class="font-bold text-black">Runtime:</span>
                        <?php echo esc_html($runtime); ?>
                      </p>
                    <?php endif; ?>

                    <?php if ($genre !== '') : ?>
                      <p>
                        <span class="font-bold text-black">Genre:</span>
                        <?php echo esc_html($genre); ?>
                      </p>
                    <?php endif; ?>

                    <?php if ($brief_synopsis !== '') : ?>
                      <p class="leading-7">
                        <span class="font-bold text-black">Brief Synopsis:</span>
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
