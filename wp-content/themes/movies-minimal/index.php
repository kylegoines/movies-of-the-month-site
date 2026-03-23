<?php
get_header();
?>

<main class="mx-auto max-w-[1000px] px-6 py-16 md:px-8 md:py-24">
  <?php get_template_part('header/site', 'header'); ?>
  <?php
  $current_view = isset($_GET['view']) ? sanitize_key(wp_unslash($_GET['view'])) : '';
  $show_home_intro = $current_view !== 'past-months';
  $home_intro_post = $show_home_intro ? movies_theme_get_home_intro() : null;
  $featured_movie = $show_home_intro ? movies_theme_get_featured_movie() : null;
  ?>

  <?php if ($home_intro_post instanceof WP_Post) : ?>
    <?php
    $home_intro_author_id = (int) $home_intro_post->post_author;
    $home_intro_link = get_author_posts_url($home_intro_author_id);
    $home_intro_name = get_the_author_meta('display_name', (int) $home_intro_post->post_author);
    ?>
    <section class="mt-[56px] max-w-[720px]">
      <div class="post-content">
        <?php echo apply_filters('the_content', $home_intro_post->post_content); ?>
      </div>

      <?php if ($home_intro_name !== '') : ?>
        <p class="theme-muted mt-4 text-sm tracking-[0.04em]">
          <a class="theme-strong font-bold transition-opacity hover:opacity-70" href="<?php echo esc_url($home_intro_link); ?>">
            &mdash;<?php echo esc_html($home_intro_name); ?>
          </a>
        </p>
      <?php endif; ?>
    </section>
  <?php endif; ?>

  <?php get_template_part('components/featured', 'movie', [
      'featured_movie' => $featured_movie,
  ]); ?>

  <?php get_template_part('components/collection', 'list'); ?>
</main>

<?php get_template_part('components/signup', null, [
    'current_view' => $current_view,
]); ?>

<?php
get_footer();
?>
