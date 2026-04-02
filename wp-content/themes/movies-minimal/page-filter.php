<?php
get_header();

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

// Available contributors used for the public Author filter on this page.
$movie_authors = get_users([
    'fields' => 'all',
    'has_published_posts' => [
        'movies',
    ],
]);

usort($movie_authors, static function (WP_User $left, WP_User $right): int {
    return strcasecmp(
        movies_theme_get_author_name($left->ID),
        movies_theme_get_author_name($right->ID)
    );
});

// Raw request values for the public filter state.
$selected_category = isset($_GET['category']) ? sanitize_title(wp_unslash($_GET['category'])) : '';
$selected_movie_author = isset($_GET['movie_author']) ? (int) wp_unslash($_GET['movie_author']) : 0;
$selected_category_exists = false;
$selected_movie_author_exists = false;
$filters_state = isset($_GET['filters']) && sanitize_key(wp_unslash($_GET['filters'])) === 'open'
    ? 'open'
    : 'closed';
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

foreach ($movie_authors as $movie_author) {
    if ($selected_movie_author === (int) $movie_author->ID) {
        $selected_movie_author_exists = true;
        break;
    }
}

if (!$selected_movie_author_exists) {
    $selected_movie_author = 0;
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

if ($selected_movie_author > 0) {
    $movies_query_args['author'] = $selected_movie_author;
}

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

if ($selected_movie_author > 0) {
    $movies_query_args['posts_per_page'] = -1;
    $movies_query_args['orderby'] = 'title';
    $movies_query_args['order'] = 'ASC';
}

// This query is passed into the Filter page results component below.
$movies_query = new WP_Query($movies_query_args);
?>

<?php get_template_part('header/site', 'header'); ?>

<main class="mx-auto max-w-[1000px] px-[32px]">
  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <article>
        <?php
        // Filter page heading and controls.
        get_template_part('components/page-filter/section', null, [
            'eyebrow_text' => $eyebrow_text,
            'filters_state' => $filters_state,
            'movie_categories' => $movie_categories,
            'movie_authors' => $movie_authors,
            'selected_category' => $selected_category,
            'selected_movie_author' => $selected_movie_author,
            'movie_filter_keys' => $movie_filter_keys,
            'selected_movie_filters' => $selected_movie_filters,
            'scale_label_config' => $scale_label_config,
        ]);
        ?>

        <?php // Optional page editor content shown between the controls and results. ?>
        <?php if (get_the_content() !== '') : ?>
          <div class="post-content rhythm-lg">
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
