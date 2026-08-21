<?php
get_header();
?>
<?php get_template_part('header/site', 'header'); ?>
<main class="mx-auto mt-10 min-h-[calc(100vh-114px)] md:max-w-[720px] lg:max-w-[1000px] px-[32px] pb-[50px] sm:min-h-[calc(100vh-154px)] sm:pb-[80px] md:mt-12 lg:min-h-[calc(100vh-232px)] lg:pb-[100px]">
  
  <?php
  $current_view = isset($_GET['view']) ? sanitize_key(wp_unslash($_GET['view'])) : '';
  $show_home_intro = $current_view !== 'past-months';
  $home_intro_post = $show_home_intro ? movies_theme_get_home_intro() : null;
  $showdown_post = $show_home_intro && movies_theme_should_show_showdown_module()
      ? movies_theme_get_current_showdown()
      : null;
  $featured_movie = $show_home_intro ? movies_theme_get_featured_movie() : null;
  $recent_activity = $show_home_intro ? movies_theme_get_recent_movie_activity() : [];
  $show_home_top_section = $show_home_intro && (
      $home_intro_post instanceof WP_Post
      || $showdown_post instanceof WP_Post
      || $featured_movie instanceof WP_Post
  );
  ?>

  <?php if ($show_home_top_section) : ?>
    <section class="md:grid gap-10 lg:grid-cols-[minmax(0,1fr)_280px] lg:items-start">
      <?php if ($showdown_post instanceof WP_Post || $featured_movie instanceof WP_Post) : ?>
        <div class="hidden lg:order-2 lg:block lg:sticky lg:top-[40px]">
          <?php if ($featured_movie instanceof WP_Post) : ?>
          <aside class="theme-accent overflow-hidden border-3 border pt-0 px-0 pb-5 lg:min-h-[120px]">
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
            $featured_spotlight_quote = movies_theme_get_spotlight_quote($featured_movie_id);
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
                'class' => movies_theme_get_poster_image_class($featured_movie_id, 'h-auto w-full object-cover'),
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
                    <?php if ($featured_spotlight_quote !== '') : ?>
                      <a class="block no-underline transition-opacity hover:opacity-70" href="<?php echo esc_url(get_permalink($featured_movie_id)); ?>">
                        <div class="post-content mb-6 text-2xl font-bold leading-tight [&_p]:font-bold">
                          <?php echo apply_filters('the_content', $featured_spotlight_quote); ?>
                        </div>
                      </a>
                  <?php endif; ?>

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

          <?php if ($showdown_post instanceof WP_Post) : ?>
            <div class="<?php echo $featured_movie instanceof WP_Post ? 'mt-4' : ''; ?>">
              <?php get_template_part('components/showdown', 'module', ['showdown' => $showdown_post]); ?>
            </div>
          <?php endif; ?>

          <div class="pt-4">
            <button
              class="spotlight-mailing-button"
              type="button"
              data-spotlight-mailing-modal-open
              aria-expanded="false"
              aria-controls="spotlight-mailing-modal"
            >
              Join the Mailing List
            </button>
          </div>
        </div>
      <?php endif; ?>

      <div class="mx-auto max-w-[720px] lg:order-1">
        <?php if ($home_intro_post instanceof WP_Post) : ?>
          <div class="post-content">
            <?php echo apply_filters('the_content', $home_intro_post->post_content); ?>
          </div>
        <?php endif; ?>

        <?php if ($showdown_post instanceof WP_Post || $featured_movie instanceof WP_Post) : ?>
          <div class="mt-6 mb-10 lg:hidden">
          <?php if ($featured_movie instanceof WP_Post) : ?>
          <aside class="theme-accent overflow-hidden border-3 border pt-0 px-0 pb-5">
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
            $featured_spotlight_quote = movies_theme_get_spotlight_quote($featured_movie_id);
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
                'class' => movies_theme_get_poster_image_class($featured_movie_id, 'h-auto w-full object-cover'),
                'loading' => 'lazy',
            ]);
            ?>
            <?php if ($featured_sidebar_poster !== '') : ?>
              <div class="px-5 md:grid md:grid-cols-[220px_minmax(0,1fr)] md:items-start md:gap-8">
                <a class="mb-6 block no-underline md:mb-0" href="<?php echo esc_url(get_permalink($featured_movie_id)); ?>">
                  <div class="relative aspect-[2/3] overflow-hidden md:aspect-auto md:min-h-[320px]">
                    <?php echo $featured_sidebar_poster; ?>
                  </div>
                </a>

                <div class="theme-body mb-5 min-w-0 space-y-2 text-sm md:flex md:min-h-[320px] md:flex-col md:justify-center lg:mb-5">
                  <?php if ($featured_spotlight_quote !== '') : ?>
                    <a class="block no-underline transition-opacity hover:opacity-70" href="<?php echo esc_url(get_permalink($featured_movie_id)); ?>">
                      <div class="post-content mb-6 min-w-0 break-words text-xl font-bold leading-tight [overflow-wrap:anywhere] xs:text-2xl md:mt-6 md:text-4xl [&_p]:font-bold [&_.quote-huge]:text-[40px] xs:[&_.quote-huge]:text-[48px] md:[&_.quote-huge]:text-[60px]">
                        <?php echo apply_filters('the_content', $featured_spotlight_quote); ?>
                      </div>
                    </a>
                  <?php endif; ?>

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

          <?php if ($showdown_post instanceof WP_Post) : ?>
            <div class="<?php echo $featured_movie instanceof WP_Post ? 'mt-4' : ''; ?>">
              <?php get_template_part('components/showdown', 'module', ['showdown' => $showdown_post]); ?>
            </div>
          <?php endif; ?>

            <div class="pt-4">
              <button
                class="spotlight-mailing-button"
                type="button"
                data-spotlight-mailing-modal-open
                aria-expanded="false"
                aria-controls="spotlight-mailing-modal"
              >
                Join the Mailing List
              </button>
            </div>
          </div>
        <?php endif; ?>

        <div class="rhythm-lg">
          <?php get_template_part('components/collection', 'list', ['activity_items' => $recent_activity]); ?>
        </div>
      </div>
    </section>
  <?php else : ?>
    <?php get_template_part('components/collection', 'list'); ?>
  <?php endif; ?>

  
</main>

<?php
get_footer();
?>
