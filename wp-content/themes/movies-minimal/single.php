<?php
get_header();
?>

<main class="mx-auto max-w-[1000px] px-6 py-16 md:px-8 md:py-24">
  <?php get_template_part('header/site', 'header'); ?>

  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <?php
      $rating = function_exists('get_field') ? (int) get_field('rating', get_the_ID()) : 0;
      $has_rating = $rating >= 1 && $rating <= 5;
      ?>
      <article>
        <header class="mt-[48px]">
          <h1 class="text-4xl tracking-[-0.06em] text-black md:text-6xl">
            <?php the_title(); ?>
          </h1>
        </header>

        <div class="post-content max-w-[720px]">
          <?php the_content(); ?>
          <?php if ($has_rating) : ?>
            <p class="mt-3 text-sm uppercase tracking-[0.18em] text-neutral-500 md:text-base">
              <?php echo esc_html($rating); ?>/5 stars
            </p>
          <?php endif; ?>
        </div>
      </article>
    <?php endwhile; ?>
  <?php endif; ?>
</main>

<?php
get_footer();
?>
