<?php
get_header();
?>
<?php get_template_part('header/site', 'header'); ?>
<main class="mx-auto max-w-[1000px] px-[32px]">
  
  <?php
  $current_view = isset($_GET['view']) ? sanitize_key(wp_unslash($_GET['view'])) : '';
  $show_home_intro = $current_view !== 'past-months';
  $home_intro_post = $show_home_intro ? movies_theme_get_home_intro() : null;
  $featured_movie = $show_home_intro ? movies_theme_get_featured_movie() : null;
  $show_home_top_section = $show_home_intro && (
      $home_intro_post instanceof WP_Post
      || $featured_movie instanceof WP_Post
  );
  ?>

  <?php if ($show_home_top_section) : ?>
    <section class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_280px] lg:items-start">
      <div class="max-w-[720px]">
        <?php if ($home_intro_post instanceof WP_Post) : ?>
          <div class="post-content">
            <?php echo apply_filters('the_content', $home_intro_post->post_content); ?>
          </div>
        <?php endif; ?>

        <div class="rhythm-lg">
          <?php get_template_part('components/collection', 'list'); ?>
        </div>
      </div>

      <?php if ($featured_movie instanceof WP_Post) : ?>
        <aside class="theme-accent hidden border border-3 pt-4 px-0 pb-5 lg:block lg:sticky lg:top-8 lg:min-h-[120px]">
          <div class="spotlight-marquee px-0">
            <div class="spotlight-marquee__track">
              <span>Spotlight</span>
              <span class="spotlight-marquee__dot" aria-hidden="true"></span>
              <span>Spotlight</span>
              <span class="spotlight-marquee__dot" aria-hidden="true"></span>
              <span>Spotlight</span>
              <span class="spotlight-marquee__dot" aria-hidden="true"></span>
              <span>Spotlight</span>
              <span class="spotlight-marquee__dot" aria-hidden="true"></span>
            </div>
          </div>
          <?php
          $featured_movie_id = $featured_movie->ID;
          $featured_sidebar_year = movies_theme_get_year($featured_movie_id);
          $featured_sidebar_runtime = movies_theme_get_runtime($featured_movie_id);
          $featured_sidebar_genre = movies_theme_get_movie_category_list($featured_movie_id);
          $featured_sidebar_gallery_ids = function_exists('get_field')
              ? get_field('spotlight_gallery', $featured_movie_id)
              : [];
          $featured_sidebar_gallery_ids = is_array($featured_sidebar_gallery_ids)
              ? array_values(array_filter(array_map(static function ($gallery_item): int {
                  if (is_array($gallery_item) && isset($gallery_item['ID'])) {
                      return (int) $gallery_item['ID'];
                  }

                  if (is_object($gallery_item) && isset($gallery_item->ID)) {
                      return (int) $gallery_item->ID;
                  }

                  return (int) $gallery_item;
              }, $featured_sidebar_gallery_ids)))
              : [];
          $featured_sidebar_poster = get_the_post_thumbnail($featured_movie_id, 'large', [
              'class' => 'h-auto w-full object-cover',
              'loading' => 'lazy',
          ]);
          ?>
          <?php if ($featured_sidebar_poster !== '') : ?>
            <div class="px-5 lg:px-7">
              <a class="mb-6 block no-underline" href="<?php echo esc_url(get_permalink($featured_movie_id)); ?>">
                <div
                  class="spotlight-gallery relative aspect-[2/3] overflow-hidden"
                  <?php echo $featured_sidebar_gallery_ids !== [] ? 'data-spotlight-gallery' : ''; ?>
                >
                  <div class="spotlight-gallery__frame">
                    <div class="spotlight-gallery__slide spotlight-gallery__slide--active">
                      <?php echo $featured_sidebar_poster; ?>
                    </div>

                    <?php foreach ($featured_sidebar_gallery_ids as $gallery_image_id) : ?>
                      <?php
                      $gallery_image = wp_get_attachment_image($gallery_image_id, 'large', false, [
                          'class' => 'h-full w-full object-cover object-center',
                          'loading' => 'lazy',
                      ]);
                      ?>
                      <?php if ($gallery_image !== '') : ?>
                        <div class="spotlight-gallery__slide" data-spotlight-gallery-slide>
                          <?php echo $gallery_image; ?>
                        </div>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </div>
                </div>
              </a>

              <div class="theme-body mb-5 space-y-2 text-sm">
                <p><span class="theme-strong font-bold">Film:</span> <?php echo esc_html(get_the_title($featured_movie_id)); ?></p>

                <?php if ($featured_sidebar_year !== '') : ?>
                  <p><span class="theme-strong font-bold">Year:</span> <?php echo esc_html($featured_sidebar_year); ?></p>
                <?php endif; ?>

                <?php if ($featured_sidebar_runtime !== '') : ?>
                  <p><span class="theme-strong font-bold">Runtime:</span> <?php echo esc_html($featured_sidebar_runtime); ?></p>
                <?php endif; ?>

                <?php if ($featured_sidebar_genre !== '') : ?>
                  <p><span class="theme-strong font-bold">Genre:</span> <?php echo esc_html($featured_sidebar_genre); ?></p>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>
        </aside>
      <?php endif; ?>
    </section>
  <?php else : ?>
    <?php get_template_part('components/collection', 'list'); ?>
  <?php endif; ?>

  
</main>

<?php
get_footer();
?>
