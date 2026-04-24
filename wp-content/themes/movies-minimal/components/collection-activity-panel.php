<?php
$activity_items = $args['activity_items'] ?? [];

if (!is_array($activity_items) || $activity_items === []) {
    return;
}

$top_activity_count = max(array_map(static function (array $activity_item): int {
    return ($activity_item['type'] ?? 'movies') === 'movies'
        ? (int) ($activity_item['count'] ?? 0)
        : 0;
}, $activity_items));
$top_contributor_badge = movies_theme_get_inline_svg('images/badge-basic.svg', 'activity-log__top-badge');
$top_contributor_badge = is_string($top_contributor_badge)
    ? preg_replace('/<svg\b/', '<svg style="color:#1d4ed8"', $top_contributor_badge, 1)
    : '';
$award_items = array_values(array_filter($activity_items, static function (array $activity_item) use ($top_activity_count): bool {
    return ($activity_item['type'] ?? 'movies') === 'movies'
        && (int) ($activity_item['count'] ?? 0) === $top_activity_count
        && $top_activity_count > 0;
}));
?>

<div
  class="collection-activity-panel-overlay"
  data-collection-activity-overlay
  data-state="closed"
  aria-hidden="true"
></div>

<aside
  class="collection-activity-panel"
  data-collection-activity-panel
  id="collection-activity-panel"
  data-state="closed"
  aria-hidden="true"
>
  <div class="collection-activity-panel__header flex items-center justify-between gap-4">
    <h2 class="theme-strong text-xl tracking-[-0.04em] md:text-2xl">Recent Activity</h2>
    <button
      class="theme-strong theme-border cursor-pointer border bg-transparent px-3 py-2 text-sm font-bold tracking-[0.04em] transition-opacity hover:opacity-70"
      type="button"
      data-collection-activity-close
      aria-label="Close activity"
    >
      Close
    </button>
  </div>

  <?php if ($award_items !== [] && $top_contributor_badge !== '') : ?>
    <section class="collection-activity-panel__awards">
      <p class="theme-muted mb-3 text-xs font-bold uppercase tracking-[0.18em]">Awards</p>
      <div class="space-y-3">
        <?php foreach ($award_items as $award_item) : ?>
          <div class="theme-body text-sm md:text-base">
            <div class="mb-1 inline-flex items-center gap-1.5 text-[#1d4ed8]">
              <span class="inline-flex h-4 w-4 items-center justify-center">
                <?php echo $top_contributor_badge; ?>
              </span>
              <span class="text-[11px] font-bold uppercase tracking-[0.08em] text-[#1d4ed8]">
                <?php echo esc_html__('Most movies added this month', 'movies-minimal'); ?>
              </span>
            </div>
            <div class="pl-[22px]">
              <a
                class="theme-strong font-bold transition-opacity hover:opacity-70 no-underline"
                href="<?php echo esc_url(get_author_posts_url((int) ($award_item['author_id'] ?? 0))); ?>"
              >
                <?php echo esc_html($award_item['author_name'] ?? ''); ?>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <div class="collection-activity-panel__divider" aria-hidden="true"></div>

  <section class="collection-activity-panel__timeline-section">
    <p class="theme-muted mb-4 text-xs font-bold uppercase tracking-[0.18em]">Activity Timeline</p>

    <ul class="collection-activity-panel__timeline space-y-6">
    <?php foreach ($activity_items as $activity_item) : ?>
      <?php
      $is_movie_activity = ($activity_item['type'] ?? 'movies') === 'movies';
      $activity_movie_ids = $is_movie_activity ? array_values(array_filter(array_map('intval', $activity_item['movie_ids'] ?? []))) : [];
      ?>
      <li class="collection-activity-panel__item theme-body text-sm md:text-base">
        <a
          class="theme-strong font-bold transition-opacity hover:opacity-70 no-underline"
          href="<?php echo esc_url(get_author_posts_url((int) ($activity_item['author_id'] ?? 0))); ?>"
        >
          <?php echo esc_html($activity_item['author_name'] ?? ''); ?>
        </a>
        <?php if ($is_movie_activity) : ?>
          <?php echo esc_html(sprintf(
              ' contributed %s %s',
              number_format_i18n((int) ($activity_item['count'] ?? 0)),
              _n('movie', 'movies', (int) ($activity_item['count'] ?? 0), 'movies-minimal')
          )); ?>
        <?php else : ?>
          <?php echo esc_html__(' added a featured article for ', 'movies-minimal'); ?>
          <a
            class="theme-strong font-bold transition-opacity hover:opacity-70 no-underline"
            href="<?php echo esc_url(get_permalink((int) ($activity_item['movie_id'] ?? 0))); ?>"
          >
            <?php echo esc_html($activity_item['movie_title'] ?? ''); ?>
          </a>
        <?php endif; ?>
        <?php if ($activity_movie_ids !== []) : ?>
          <div class="mt-3 flex flex-wrap gap-2" aria-hidden="true">
            <?php foreach ($activity_movie_ids as $activity_movie_id) : ?>
              <?php
              $activity_poster = get_the_post_thumbnail($activity_movie_id, 'thumbnail', [
                  'class' => 'h-full w-full object-cover',
                  'loading' => 'lazy',
              ]);
              ?>
              <?php if ($activity_poster !== '') : ?>
                <span class="block h-12 w-8 overflow-hidden border border-black bg-white">
                  <?php echo $activity_poster; ?>
                </span>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
    </ul>
  </section>
</aside>
