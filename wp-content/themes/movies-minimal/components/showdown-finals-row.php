<?php
$poster_ids = $args['poster_ids'] ?? [];
$poster_ids = is_array($poster_ids)
    ? array_values(array_filter(array_map('intval', $poster_ids)))
    : [];

if ($poster_ids === []) {
    return;
}
?>

<div class="showdown-finals-row" role="region" aria-label="Finalist posters">
  <div class="showdown-finals-row__posters">
    <?php foreach ($poster_ids as $poster_id) : ?>
      <?php
      $poster = wp_get_attachment_image($poster_id, 'medium_large', false, [
          'class' => 'h-full w-full object-cover object-center',
          'loading' => 'lazy',
      ]);
      ?>
      <?php if ($poster !== '') : ?>
        <div class="showdown-finals-row__poster">
          <?php echo $poster; ?>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</div>
