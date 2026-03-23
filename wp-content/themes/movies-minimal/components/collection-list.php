<?php if (have_posts()) : ?>
  <ul class="mt-[96px] flex flex-col">
    <?php while (have_posts()) : the_post(); ?>
      <?php $summary = movies_theme_get_list_summary(get_the_ID()); ?>
      <li>
        <a class="block no-underline" href="<?php the_permalink(); ?>">
          <div class="flex flex-col">
            <div class="flex">
                <h2 class="theme-strong text-3xl tracking-[-0.05em] md:text-5xl shrink-0">
                    <?php the_title(); ?>
                </h2>
                <div class="accent-rule mt-auto h-[3px] w-[47px] w-full ml-7"></div>
            </div>
            <div class="max-w-2xl mt-6">
              <?php if ($summary !== '') : ?>
                <div class="theme-strong mt-2 text-lg font-bold [&_p]:m-0">
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
