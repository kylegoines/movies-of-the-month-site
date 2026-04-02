<?php

// The prepared movie query is passed in from page-filter.php.
// This component is responsible only for rendering the current result set.
$movies_query = $args['movies_query'] ?? null;
?>

<div data-filter-results>
  <?php if ($movies_query instanceof WP_Query && $movies_query->have_posts()) : ?>
    <div class="rhythm-lg">
      <?php
      get_template_part('components/movie-grid-full', null, [
          'movies' => $movies_query->posts,
      ]);
      ?>
    </div>
  <?php else : ?>
    <p class="theme-muted rhythm-lg py-6 text-sm uppercase tracking-[0.18em]">No match yet. Try adjusting the filters.</p>
  <?php endif; ?>
</div>
