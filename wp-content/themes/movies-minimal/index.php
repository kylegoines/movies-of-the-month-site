<?php
get_header();
?>

<main class="mx-auto max-w-4xl px-6 py-16 md:px-8 md:py-24">
  <?php get_template_part('header/site', 'header'); ?>

  <?php if (have_posts()) : ?>
    <ul class="border-t border-black">
      <?php while (have_posts()) : the_post(); ?>
        <?php $intro = movies_minimal_get_post_intro(get_the_ID()); ?>
        <li class="border-b border-black py-5">
          <a class="block no-underline" href="<?php the_permalink(); ?>">
            <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between md:gap-8">
              <div class="max-w-2xl">
                <span class="block text-xl font-medium text-black md:text-2xl"><?php the_title(); ?></span>
                <?php if ($intro !== '') : ?>
                  <div class="mt-2 text-sm leading-6 text-neutral-600 md:text-base [&_p]:m-0">
                    <?php echo wp_kses_post($intro); ?>
                  </div>
                <?php endif; ?>
              </div>
              <time class="shrink-0 text-xs uppercase tracking-[0.18em] text-neutral-500 md:pt-1 md:text-sm" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                <?php echo esc_html(get_the_date('M j, Y')); ?>
              </time>
            </div>
          </a>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php else : ?>
    <p class="py-6 text-sm uppercase tracking-[0.18em] text-neutral-500">No posts yet.</p>
  <?php endif; ?>
</main>

<?php
get_footer();
?>
