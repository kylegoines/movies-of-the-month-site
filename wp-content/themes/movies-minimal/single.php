<?php
get_header();
?>

<main class="mx-auto max-w-[1000px] px-6 py-16 md:px-8 md:py-24">
  <?php get_template_part('header/site', 'header'); ?>

  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <?php $subtitle = movies_minimal_get_subtitle(get_the_ID()); ?>
      <article>
        <header class="mt-[48px]">
          <h1 class="theme-strong text-4xl tracking-[-0.06em] md:text-6xl">
            <?php the_title(); ?>
          </h1>
          <?php if ($subtitle !== '') : ?>
            <p class="theme-body mt-3 text-lg font-bold md:text-xl">
              <?php echo esc_html($subtitle); ?>
            </p>
          <?php endif; ?>
        </header>

        <div class="post-content max-w-[720px]">
          <?php the_content(); ?>
        </div>
      </article>
    <?php endwhile; ?>
  <?php endif; ?>
</main>

<?php
get_footer();
?>
