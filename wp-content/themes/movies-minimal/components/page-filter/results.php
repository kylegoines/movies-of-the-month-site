<?php

// The prepared movie query is passed in from page-filter.php.
// This component is responsible only for rendering the current result set.
$movies_query = $args['movies_query'] ?? null;
?>

<div data-filter-results>
  <?php if ($movies_query instanceof WP_Query && $movies_query->have_posts()) : ?>
    <section class="rhythm-lg grid grid-cols-2 gap-x-6 gap-y-10 md:grid-cols-3 xl:grid-cols-5">
      <?php while ($movies_query->have_posts()) : ?>
      <?php
      // Per-movie values used by each card in the Filter page result grid.
      $movies_query->the_post();
      $subtitle = movies_theme_get_subtitle(get_the_ID());
      $is_hidden_gem = movies_theme_is_hidden_gem(get_the_ID());
      $gem_badge = $is_hidden_gem
          ? movies_theme_get_inline_svg('images/gemsingle.svg', 'theme-gem-badge')
          : '';
      $poster = get_the_post_thumbnail(get_the_ID(), 'large', [
          'class' => 'h-auto w-full object-cover',
          'loading' => 'lazy',
      ]);
      ?>
        <article>
          <a class="movie-card block no-underline" href="<?php the_permalink(); ?>">
            <?php if ($poster !== '') : ?>
              <div class="poster-frame theme-surface <?php echo $is_hidden_gem ? 'poster-frame--hidden-gem' : ''; ?>">
                <?php echo $poster; ?>
              </div>
            <?php endif; ?>

            <h3 class="mt-4 flex items-center gap-2 text-xl tracking-[-0.04em] <?php echo $is_hidden_gem ? 'movie-title--hidden-gem' : 'theme-strong'; ?>">
              <span><?php the_title(); ?></span>
              <?php if ($gem_badge !== '') : ?>
                <span class="movie-title__gem"><?php echo $gem_badge; ?></span>
              <?php endif; ?>
            </h3>

            <?php if ($subtitle !== '') : ?>
              <p class="theme-body mt-2 text-base font-bold">
                <?php echo esc_html($subtitle); ?>
              </p>
            <?php endif; ?>
          </a>
        </article>
      <?php endwhile; ?>
      <?php wp_reset_postdata(); ?>
    </section>
  <?php else : ?>
    <p class="theme-muted rhythm-lg py-6 text-sm uppercase tracking-[0.18em]">Arg! We couldnt find anything! Do you have some ideas? Lets chat!</p>
  <?php endif; ?>
</div>
