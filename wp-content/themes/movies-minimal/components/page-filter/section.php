<?php

// These values are passed in from page-filter.php and are only used by the
// Filter page heading and filter controls.
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

  <div class="mt-12">
    <div class="flex items-center justify-start gap-6">
      <button
        class="theme-strong theme-border cursor-pointer border bg-transparent px-4 py-2 text-sm font-bold tracking-[0.04em] transition-opacity hover:opacity-70"
        type="button"
        data-filter-toggle
        aria-controls="filter-drawer"
        aria-expanded="<?php echo $filters_state === 'open' ? 'true' : 'false'; ?>"
      >
        Filters
      </button>
    </div>

    <div
      class="filter-panel-overlay"
      data-filter-overlay
      data-state="<?php echo esc_attr($filters_state); ?>"
      aria-hidden="<?php echo $filters_state === 'open' ? 'false' : 'true'; ?>"
    ></div>

    <form
      action="<?php echo esc_url(get_permalink()); ?>"
      class="filter-panel"
      id="filter-drawer"
      method="get"
      data-filter-form
      data-filter-panel
      data-filter-panel-state="<?php echo esc_attr($filters_state); ?>"
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
          data-filter-close
          aria-label="Close filters"
        >
          Close
        </button>
      </div>

      <div class="filter-panel__fields grid gap-5 md:grid-cols-2">
        <?php
        // Preserves the current page context when the site is using plain
        // permalinks, so filter submissions stay on the Filter page.
        ?>
        <?php if (is_page() && !get_option('permalink_structure')) : ?>
          <input type="hidden" name="page_id" value="<?php echo esc_attr((string) get_the_ID()); ?>">
        <?php endif; ?>

        <?php
        // Used by the Filter page panel JS to keep the open/closed state
        // in sync with the URL.
        ?>
        <input type="hidden" name="filters" value="<?php echo esc_attr($filters_state); ?>" data-filter-state>

        <?php
        // Public-facing Genre filter. This is backed by the movie category taxonomy.
        ?>
        <label class="block md:col-span-2">
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

        <label class="block md:col-span-2">
          <span class="theme-muted mb-2 block text-xs font-bold tracking-[0.04em]">
            Author
          </span>
          <span class="theme-border relative block border-b">
            <select
              class="theme-body w-full appearance-none bg-transparent px-0 py-3 pr-8 text-lg font-bold focus:outline-none"
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
            <span class="theme-strong pointer-events-none absolute right-1 top-1/2 h-2.5 w-2.5 -translate-y-1/2 rotate-45 border-b-2 border-r-2" aria-hidden="true"></span>
          </span>
        </label>

        <?php foreach ($movie_filter_keys as $movie_filter_key) : ?>
          <?php
          // Public-facing mood filters. These map to the ACF movie fields and
          // preserve the active selection when the page re-renders.
          ?>
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
      </div>
    </form>
  </div>
</section>
