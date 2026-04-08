<?php

$eyebrow_text = $args['eyebrow_text'] ?? 'I’m looking for';
$filters_state = $args['filters_state'] ?? 'open';
$movie_categories = $args['movie_categories'] ?? [];
$movie_authors = $args['movie_authors'] ?? [];
$selected_category = $args['selected_category'] ?? '';
$selected_movie_author = $args['selected_movie_author'] ?? 0;
$movie_filter_keys = $args['movie_filter_keys'] ?? [];
$selected_movie_filters = $args['selected_movie_filters'] ?? [];
$scale_label_config = $args['scale_label_config'] ?? [];
?>

<?php ob_start(); ?>
  <?php if (is_page() && !get_option('permalink_structure')) : ?>
    <input type="hidden" name="page_id" value="<?php echo esc_attr((string) get_the_ID()); ?>">
  <?php endif; ?>

  <input type="hidden" name="filters" value="<?php echo esc_attr($filters_state); ?>" data-filter-state>

  <label class="filter-panel__field filter-panel__field--primary block">
    <span class="theme-muted mb-2 block text-xs font-bold tracking-[0.04em]">Genre</span>
    <span class="theme-border relative block border px-3">
      <select
        class="theme-body w-full cursor-pointer appearance-none bg-transparent px-0 py-3 pr-8 text-lg font-bold focus:outline-none"
        name="category"
        data-filter-select
      >
        <option value="">Select</option>
        <?php foreach ($movie_categories as $movie_category) : ?>
          <option value="<?php echo esc_attr($movie_category->slug); ?>" <?php selected($selected_category, $movie_category->slug); ?>>
            <?php echo esc_html($movie_category->name); ?>
          </option>
        <?php endforeach; ?>
      </select>
      <span class="theme-strong pointer-events-none absolute right-3 top-1/2 h-2.5 w-2.5 -translate-y-1/2 rotate-45 border-b-2 border-r-2" aria-hidden="true"></span>
    </span>
  </label>

  <label class="filter-panel__field filter-panel__field--primary block">
    <span class="theme-muted mb-2 block text-xs font-bold tracking-[0.04em]">Author</span>
    <span class="theme-border relative block border px-3">
      <select
        class="theme-body w-full cursor-pointer appearance-none bg-transparent px-0 py-3 pr-8 text-lg font-bold focus:outline-none"
        name="movie_author"
        data-filter-select
      >
        <option value="">Select</option>
        <?php foreach ($movie_authors as $movie_author) : ?>
          <option value="<?php echo esc_attr((string) $movie_author->ID); ?>" <?php selected((int) $selected_movie_author, (int) $movie_author->ID); ?>>
            <?php echo esc_html(movies_theme_get_author_name((int) $movie_author->ID)); ?>
          </option>
        <?php endforeach; ?>
      </select>
      <span class="theme-strong pointer-events-none absolute right-3 top-1/2 h-2.5 w-2.5 -translate-y-1/2 rotate-45 border-b-2 border-r-2" aria-hidden="true"></span>
    </span>
  </label>

  <?php foreach ($movie_filter_keys as $movie_filter_key) : ?>
    <label class="filter-panel__field filter-panel__field--mood block">
      <span class="theme-muted mb-2 block text-xs font-bold tracking-[0.04em]">
        <?php echo esc_html(ucfirst($movie_filter_key)); ?>
      </span>
      <span class="theme-border relative block border px-3">
        <select
          class="theme-body w-full cursor-pointer appearance-none bg-transparent px-0 py-3 pr-8 text-lg font-bold focus:outline-none"
          name="<?php echo esc_attr($movie_filter_key); ?>"
          data-filter-select
        >
          <option value="">Select</option>
          <?php foreach ($scale_label_config[$movie_filter_key] as $scale_value => $scale_label) : ?>
            <?php if ($scale_value === '0') : ?>
              <?php continue; ?>
            <?php endif; ?>
            <option value="<?php echo esc_attr($scale_value); ?>" <?php selected($selected_movie_filters[$movie_filter_key] ?? '', $scale_value); ?>>
              <?php echo esc_html($scale_label); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <span class="theme-strong pointer-events-none absolute right-3 top-1/2 h-2.5 w-2.5 -translate-y-1/2 rotate-45 border-b-2 border-r-2" aria-hidden="true"></span>
      </span>
    </label>
  <?php endforeach; ?>
<?php $filter_fields_markup = trim((string) ob_get_clean()); ?>

<section>
  <header>
    <p class="theme-muted mb-3 text-base font-bold tracking-[0.04em] md:text-lg">
      <?php echo esc_html($eyebrow_text); ?>
    </p>
    <div class="relative flex flex-wrap items-end gap-x-7 gap-y-3">
      <h2 class="theme-strong min-w-0 flex-auto text-3xl tracking-[-0.05em] md:text-5xl" data-filter-heading data-base-text="Movies that are...">
        <span class="opacity-90">Movies that are...</span>
      </h2>
    </div>
  </header>

  <div class="rhythm-lg">
    <div class="flex items-center justify-start gap-6 mt-9">
      <button
        class="theme-strong theme-border filter-toggle-mobile cursor-pointer border bg-transparent px-4 py-2 text-sm font-bold tracking-[0.04em] transition-opacity hover:opacity-70"
        type="button"
        data-filter-toggle-mobile
        aria-controls="filter-drawer-mobile"
        aria-expanded="<?php echo $filters_state === 'open' ? 'true' : 'false'; ?>"
      >
        Filters
      </button>
      <button
        class="theme-strong theme-border filter-toggle-desktop cursor-pointer border bg-transparent px-4 py-2 text-sm font-bold tracking-[0.04em] transition-opacity hover:opacity-70"
        type="button"
        data-filter-toggle-desktop
        aria-controls="filter-drawer-desktop"
        aria-expanded="<?php echo $filters_state === 'open' ? 'true' : 'false'; ?>"
      >
        Filters
      </button>
    </div>

    <div
      class="filter-panel-overlay"
      data-filter-overlay-mobile
      data-state="<?php echo esc_attr($filters_state); ?>"
      aria-hidden="<?php echo $filters_state === 'open' ? 'false' : 'true'; ?>"
    ></div>

    <form
      action="<?php echo esc_url(get_permalink()); ?>"
      class="filter-panel filter-panel-mobile"
      id="filter-drawer-mobile"
      method="get"
      data-filter-form-mobile
      data-filter-form-sync
      data-filter-panel-mobile
      data-filter-panel-mode="drawer"
      aria-hidden="<?php echo $filters_state === 'open' ? 'false' : 'true'; ?>"
    >
      <div class="filter-panel__header flex items-center justify-between gap-4">
        <div>
          <p class="theme-muted text-xs font-bold uppercase tracking-[0.18em]">Filter Movies</p>
          <p class="theme-body mt-2 text-sm">Narrow the list by genre, contributor, or mood to find the right recommendation.</p>
        </div>
        <button
          class="theme-strong theme-border filter-panel__close cursor-pointer border bg-transparent px-3 py-2 text-sm font-bold tracking-[0.04em] transition-opacity hover:opacity-70"
          type="button"
          data-filter-close-mobile
          aria-label="Close filters"
        >
          Close
        </button>
      </div>

      <div class="filter-panel__fields grid gap-5 md:grid-cols-2">
        <?php echo $filter_fields_markup; ?>
      </div>
    </form>

    <form
      action="<?php echo esc_url(get_permalink()); ?>"
      class="filter-panel filter-panel-desktop hidden md:block"
      id="filter-drawer-desktop"
      method="get"
      data-filter-form-desktop
      data-filter-form-sync
      data-filter-panel-desktop
      data-filter-panel-mode="accordion"
      aria-hidden="<?php echo $filters_state === 'open' ? 'false' : 'true'; ?>"
    >
      <div class="filter-panel__fields">
        <?php echo $filter_fields_markup; ?>
      </div>
    </form>
  </div>
</section>
