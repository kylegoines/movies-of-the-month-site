<?php
$activity_items = $args['activity_items'] ?? [];

if (!is_array($activity_items) || $activity_items === []) {
    return;
}
?>

<section class="mb-10 p-4 md:mb-12 border-3 mt-8">
  <div class="mb-4 flex items-end gap-4">
    <h2 class="theme-strong text-xl tracking-[-0.04em] md:text-2xl">This Months Activity</h2>
  </div>

  <ul class="space-y-3">
    <?php foreach ($activity_items as $activity_item) : ?>
      <li class="theme-body text-sm md:text-base">
        <a
          class="theme-strong font-bold transition-opacity hover:opacity-70 no-underline"
          href="<?php echo esc_url(get_author_posts_url((int) ($activity_item['author_id'] ?? 0))); ?>"
        >
          <?php echo esc_html($activity_item['author_name'] ?? ''); ?>
        </a>
        <?php echo esc_html(sprintf(
            ' contributed %s %s',
            number_format_i18n((int) ($activity_item['count'] ?? 0)),
            _n('movie', 'movies', (int) ($activity_item['count'] ?? 0), 'movies-minimal')
        )); ?>
      </li>
    <?php endforeach; ?>
  </ul>
</section>
