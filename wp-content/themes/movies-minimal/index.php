<?php
get_header();
?>

<main class="mx-auto max-w-[1000px] px-6 py-16 md:px-8 md:py-24">
  <?php get_template_part('header/site', 'header'); ?>
  <?php
  $current_view = isset($_GET['view']) ? sanitize_key(wp_unslash($_GET['view'])) : '';
  $show_home_intro = $current_view !== 'past-months';
  $home_intro_post = $show_home_intro ? movies_minimal_get_home_intro() : null;
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
        <p class="mt-4 text-sm tracking-[0.04em] text-neutral-500">
          <a class="font-bold text-black transition-opacity hover:opacity-70" href="<?php echo esc_url($home_intro_link); ?>">
            &mdash;<?php echo esc_html($home_intro_name); ?>
          </a>
        </p>
      <?php endif; ?>
    </section>
  <?php endif; ?>

  <?php if (have_posts()) : ?>
    <ul class="mt-[96px] flex flex-col">
      <?php while (have_posts()) : the_post(); ?>
        <?php $summary = movies_minimal_get_list_summary(get_the_ID()); ?>
        <li>
          <a class="block no-underline" href="<?php the_permalink(); ?>">
            <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between md:gap-8">
              <div class="max-w-2xl">
                <span class="block text-xl font-bold text-black md:text-2xl"><?php the_title(); ?></span>
                <?php if ($summary !== '') : ?>
                  <div class="mt-2 text-lg font-bold text-black [&_p]:m-0">
                    <?php echo esc_html($summary); ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </a>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php else : ?>
    <p class="py-6 text-sm uppercase tracking-[0.18em] text-neutral-500">No collections yet.</p>
  <?php endif; ?>
</main>

<?php
get_footer();
?>
