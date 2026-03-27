<?php

$eyebrow_text = $args['eyebrow_text'] ?? 'I’m looking for';
$filters_state = $args['filters_state'] ?? 'open';
$movie_categories = $args['movie_categories'] ?? [];
$selected_category = $args['selected_category'] ?? '';
$movie_filter_keys = $args['movie_filter_keys'] ?? [];
$selected_movie_filters = $args['selected_movie_filters'] ?? [];
$scale_label_config = $args['scale_label_config'] ?? [];
?>

<section>
  <header>
    <p class="theme-muted mb-3 text-base font-bold tracking-[0.04em] md:text-lg">
      <?php echo esc_html($eyebrow_text); ?>
    </p>
    <div class="relative flex">
      <h2 class="theme-strong shrink-0 text-3xl tracking-[-0.05em] md:text-5xl" data-filter-heading data-base-text="Movies that are...">
        <span class="opacity-90">Movies that are...</span>
      </h2>
      <div class="accent-rule mt-auto ml-7 h-[3px] w-full"></div>
    </div>
  </header>

  <div class="mt-12">
    <div class="flex items-center justify-end gap-6">
      <button
        class="theme-strong theme-border cursor-pointer border bg-transparent px-4 py-2 text-sm font-bold tracking-[0.04em] transition-opacity hover:opacity-70"
        type="button"
        data-filter-toggle
        aria-expanded="<?php echo $filters_state === 'open' ? 'true' : 'false'; ?>"
      >
        <?php echo $filters_state === 'open' ? 'Hide Filters' : 'Show Filters'; ?>
      </button>
    </div>

    <form
      action="<?php echo esc_url(get_permalink()); ?>"
      class="filter-panel grid gap-5 md:grid-cols-2 xl:grid-cols-5 xl:items-end"
      method="get"
      data-filter-form
      data-filter-panel
      data-filter-panel-state="<?php echo esc_attr($filters_state); ?>"
    >
      <?php if (is_page() && !get_option('permalink_structure')) : ?>
        <input type="hidden" name="page_id" value="<?php echo esc_attr((string) get_the_ID()); ?>">
      <?php endif; ?>
      <input type="hidden" name="filters" value="<?php echo esc_attr($filters_state); ?>" data-filter-state>
      <label class="block md:col-span-2 xl:col-span-5">
        <span class="theme-muted mb-2 block text-xs font-bold tracking-[0.04em]">
          Genre
        </span>
        <span class="theme-border relative block border-b">
          <select
            class="theme-body w-full appearance-none bg-transparent px-0 py-3 pr-8 text-lg font-bold focus:outline-none"
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
          <span class="theme-strong pointer-events-none absolute right-1 top-1/2 h-2.5 w-2.5 -translate-y-1/2 rotate-45 border-b-2 border-r-2" aria-hidden="true"></span>
        </span>
      </label>

      <?php foreach ($movie_filter_keys as $movie_filter_key) : ?>
        <label class="block">
          <span class="theme-muted mb-2 block text-xs font-bold tracking-[0.04em]">
            <?php echo esc_html(ucfirst($movie_filter_key)); ?>
          </span>
          <span class="theme-border relative block border-b">
            <select
              class="theme-body w-full appearance-none bg-transparent px-0 py-3 pr-8 text-lg font-bold focus:outline-none"
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
            <span class="theme-strong pointer-events-none absolute right-1 top-1/2 h-2.5 w-2.5 -translate-y-1/2 rotate-45 border-b-2 border-r-2" aria-hidden="true"></span>
          </span>
        </label>
      <?php endforeach; ?>
    </form>
  </div>
</section>
