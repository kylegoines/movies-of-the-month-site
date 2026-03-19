<?php
get_header();
?>

<main class="mx-auto max-w-4xl px-6 py-16 md:px-8 md:py-24">
  <?php get_template_part('header/site', 'header'); ?>

  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <?php
      $rating = function_exists('get_field') ? (int) get_field('rating', get_the_ID()) : 0;
      $has_rating = $rating >= 1 && $rating <= 5;
      ?>
      <article>
        <header class="border-b border-black pb-8">
          <p class="text-xs uppercase tracking-[0.18em] text-neutral-500 md:text-sm">
            <?php echo esc_html(get_the_date('M j, Y')); ?>
          </p>
          <h1 class="mt-3 text-4xl font-semibold tracking-[-0.06em] text-black md:text-6xl">
            <?php the_title(); ?>
          </h1>
          <?php if ($has_rating) : ?>
            <p class="mt-3 text-sm uppercase tracking-[0.18em] text-neutral-500 md:text-base">
              <?php echo esc_html($rating); ?>/5 stars
            </p>
          <?php endif; ?>
        </header>

        <div class="prose prose-neutral mt-8 max-w-none text-base leading-7 prose-headings:font-semibold prose-headings:tracking-[-0.04em] prose-a:text-black">
          <?php the_content(); ?>
        </div>
      </article>
    <?php endwhile; ?>
  <?php endif; ?>
</main>

<?php
get_footer();
?>
