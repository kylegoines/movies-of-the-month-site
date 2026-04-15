
<?php
$activity_items = $args['activity_items'] ?? [];
?>

<?php if (have_posts()) : ?>
  <ul class="mt-4 lg:mt-13 flex flex-col">
    <?php while (have_posts()) : the_post(); ?>
      <?php
      $collection_index = (int) $wp_query->current_post;
      $summary = movies_theme_get_list_summary(get_the_ID());
      $collection_movie_ids = movies_theme_get_collection_movies(get_the_ID());
      $collection_poster_urls = array_values(array_filter(array_map(static function (int $movie_id): string {
          return (string) get_the_post_thumbnail_url($movie_id, 'large');
      }, $collection_movie_ids)));
      ?>
      <li class="border-t border-black py-8 first:border-t-0 first:pt-0 last:pb-0">
        <a class="block no-underline" href="<?php the_permalink(); ?>">
          <div class="flex flex-col">
            <div class="flex">
                <h2
                  class="collection-list__title theme-strong shrink-0 text-6xl leading-none tracking-[-0.05em] md:text-8xl lg:text-[90px]"
                  <?php if ($collection_poster_urls !== []) : ?>
                    data-collection-title-flicker
                    data-collection-title-images="<?php echo esc_attr(wp_json_encode($collection_poster_urls)); ?>"
                  <?php endif; ?>
                >
                    <span class="collection-list__title-text"><?php the_title(); ?></span>
                </h2>
            </div>
            <?php if ($summary !== '') : ?>
              <div class="mt-4 max-w-2xl">
                <div class="theme-strong text-lg font-bold [&_p]:m-0">
                  <?php echo esc_html($summary); ?>
                </div>
              </div>
            <?php endif; ?>

            <?php if ($collection_index === 0 && is_array($activity_items) && $activity_items !== []) : ?>
              <?php get_template_part('components/activity-log', null, ['activity_items' => $activity_items]); ?>
            <?php endif; ?>
          </div>
        </a>
      </li>
    <?php endwhile; ?>
  </ul>
<?php else : ?>
  <p class="theme-muted py-6 text-sm uppercase tracking-[0.18em]">No collections yet.</p>
<?php endif; ?>
