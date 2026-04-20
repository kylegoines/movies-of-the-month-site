<?php
$activity_items = $args['activity_items'] ?? [];

if (!is_array($activity_items) || $activity_items === []) {
    return;
}

$top_activity_count = max(array_map(static function (array $activity_item): int {
    return (int) ($activity_item['count'] ?? 0);
}, $activity_items));
$top_contributor_badge = movies_theme_get_inline_svg('images/badge-basic.svg', 'activity-log__top-badge');
$top_contributor_badge = is_string($top_contributor_badge)
    ? preg_replace('/<svg\b/', '<svg style="color:#1d4ed8"', $top_contributor_badge, 1)
    : '';
?>

<section class="mb-10 p-4 md:mb-12 border-3 mt-8">
  <div class="mb-4 flex items-end gap-4">
    <h2 class="theme-strong text-xl tracking-[-0.04em] md:text-2xl">This Months Activity</h2>
  </div>

  <ul class="space-y-3">
    <?php foreach ($activity_items as $activity_item) : ?>
      <?php $is_top_contributor = (int) ($activity_item['count'] ?? 0) === $top_activity_count && $top_activity_count > 0; ?>
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
        <?php if ($is_top_contributor && $top_contributor_badge !== '') : ?>
          <span
            class="mt-2 block w-fit border px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.08em] text-[#1d4ed8]"
            title="<?php echo esc_attr__('Most movies added this month', 'movies-minimal'); ?>"
            aria-label="<?php echo esc_attr__('Most movies added this month', 'movies-minimal'); ?>"
          >
            <span class="inline-flex items-center gap-1.5 relative top-[2px]">
              <span class="inline-flex h-4 w-4 items-center justify-center">
                <?php echo $top_contributor_badge; ?>
              </span>
              <span><?php echo esc_html__('Most movies added this month', 'movies-minimal'); ?></span>
            </span>
          </span>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
</section>
