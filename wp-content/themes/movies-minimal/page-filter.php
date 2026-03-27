<?php
get_header();
get_template_part('header/site', 'header');

// Shared label config for the mood scale dropdowns rendered in the filter controls.
$scale_label_config = movies_theme_get_scale_label_config();

// Available movie categories used for the public Genre filter on this page.
$movie_categories = get_terms([
    'taxonomy' => 'category',
    'hide_empty' => true,
    'object_ids' => get_posts([
        'post_type' => 'movies',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'no_found_rows' => true,
    ]),
]);

// Raw request values for the public filter state.
$selected_category = isset($_GET['category']) ? sanitize_title(wp_unslash($_GET['category'])) : '';
$selected_category_exists = false;
$filters_state = isset($_GET['filters']) && sanitize_key(wp_unslash($_GET['filters'])) === 'closed'
    ? 'closed'
    : 'open';
$eyebrow_text = isset($_GET['eyebrow'])
    ? trim(wp_strip_all_tags(sanitize_text_field(wp_unslash($_GET['eyebrow']))))
    : 'I’m looking for';

// Fallback eyebrow text used when no custom URL value is provided.
if ($eyebrow_text === '') {
    $eyebrow_text = 'I’m looking for';
}

// These are the ACF-backed movie mood filters available on the Filter page.
$movie_filter_keys = ['funny', 'scary', 'sadness', 'pacing'];
$selected_movie_filters = [];
$movie_meta_query = [];
$movie_tax_query = [];

// Validate the incoming category slug against real movie categories before using it.
foreach ($movie_categories as $movie_category) {
    if ($selected_category === $movie_category->slug) {
        $selected_category_exists = true;
        break;
    }
}

if (!$selected_category_exists) {
    $selected_category = '';
}

// Category filtering is handled as a taxonomy query because Genre maps to categories.
if ($selected_category !== '') {
    $movie_tax_query[] = [
        'taxonomy' => 'category',
        'field' => 'slug',
        'terms' => $selected_category,
    ];
}

// Build the meta query from the selected mood dropdown values.
foreach ($movie_filter_keys as $movie_filter_key) {
    $selected_value = isset($_GET[$movie_filter_key]) ? sanitize_text_field(wp_unslash($_GET[$movie_filter_key])) : '';
    $is_valid_value = $selected_value === '' || array_key_exists($selected_value, $scale_label_config[$movie_filter_key]);

    $selected_movie_filters[$movie_filter_key] = $is_valid_value ? $selected_value : '';

    if ($selected_movie_filters[$movie_filter_key] === '') {
        continue;
    }

    $movie_meta_query[] = [
        'key' => $movie_filter_key,
        'value' => $selected_movie_filters[$movie_filter_key],
        'compare' => '=',
    ];
}

// Default state shows a larger random sampling of movies before any filters are applied.
$movies_query_args = [
    'post_type' => 'movies',
    'post_status' => 'publish',
    'posts_per_page' => 48,
    'orderby' => 'rand',
];

// Once a mood filter is active, switch to the full filtered list in title order.
if ($movie_meta_query !== []) {
    $movies_query_args['posts_per_page'] = -1;
    $movies_query_args['orderby'] = 'title';
    $movies_query_args['order'] = 'ASC';
    $movies_query_args['meta_query'] = $movie_meta_query;
}

// Category filtering uses the same full-list ordering as the mood filters.
if ($movie_tax_query !== []) {
    $movies_query_args['posts_per_page'] = -1;
    $movies_query_args['orderby'] = 'title';
    $movies_query_args['order'] = 'ASC';
    $movies_query_args['tax_query'] = $movie_tax_query;
}

// This query is passed into the Filter page results component below.
$movies_query = new WP_Query($movies_query_args);
?>

<main class="mx-auto max-w-[1000px] px-6 pt-2 pb-16 md:px-8 md:pt-2 md:pb-24">
  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <article class="mt-[56px]">
        <?php
        // Filter page heading and controls.
        get_template_part('components/page-filter/section', null, [
            'eyebrow_text' => $eyebrow_text,
            'filters_state' => $filters_state,
            'movie_categories' => $movie_categories,
            'selected_category' => $selected_category,
            'movie_filter_keys' => $movie_filter_keys,
            'selected_movie_filters' => $selected_movie_filters,
            'scale_label_config' => $scale_label_config,
        ]);
        ?>

        <?php // Optional page editor content shown between the controls and results. ?>
        <?php if (get_the_content() !== '') : ?>
          <div class="post-content mt-8">
            <?php the_content(); ?>
          </div>
        <?php endif; ?>

        <?php
        // Filter page result grid rendered from the query prepared above.
        get_template_part('components/page-filter/results', null, [
            'movies_query' => $movies_query,
        ]);
        ?>
      </article>
    <?php endwhile; ?>
  <?php endif; ?>
</main>

<?php
get_footer();
?>
