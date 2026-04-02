<?php if (have_posts()) : ?>
  <ul class="mt-4 lg:mt-13 flex flex-col gap-8 lg:gap-10">
    <?php while (have_posts()) : the_post(); ?>
      <?php $summary = movies_theme_get_list_summary(get_the_ID()); ?>
      <li>
        <a class="block no-underline" href="<?php the_permalink(); ?>">
          <div class="flex flex-col">
            <div class="flex">
                <h2 class="theme-strong text-3xl tracking-[-0.05em] lg:text-5xl shrink-0">
                    <?php the_title(); ?>
                </h2>
            </div>
            <div class="max-w-2xl mb-6">
              <?php if ($summary !== '') : ?>
                <div class="theme-strong mb-2 text-lg font-bold [&_p]:m-0">
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
  <p class="theme-muted py-6 text-sm uppercase tracking-[0.18em]">No collections yet.</p>
<?php endif; ?>
