
<?php if (have_posts()) : ?>
  <ul class="mt-4 lg:mt-13 flex flex-col">
    <?php while (have_posts()) : the_post(); ?>
      <?php $summary = movies_theme_get_list_summary(get_the_ID()); ?>
      <li class="border-t border-black py-8 first:border-t-0 first:pt-0 last:pb-0">
        <a class="block no-underline" href="<?php the_permalink(); ?>">
          <div class="flex flex-col">
            <div class="flex">
                <h2 class="theme-strong shrink-0 text-3xl leading-none tracking-[-0.05em] lg:text-5xl">
                    <?php the_title(); ?>
                </h2>
            </div>
          </div>
        </a>
      </li>
    <?php endwhile; ?>
  </ul>
<?php else : ?>
  <p class="theme-muted py-6 text-sm uppercase tracking-[0.18em]">No collections yet.</p>
<?php endif; ?>
