<?php
get_header();

$scale_label_config = movies_theme_get_scale_label_config();
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
$selected_category = isset($_GET['category']) ? sanitize_title(wp_unslash($_GET['category'])) : '';
$selected_category_exists = false;
$filters_state = isset($_GET['filters']) && sanitize_key(wp_unslash($_GET['filters'])) === 'closed'
    ? 'closed'
    : 'open';
$eyebrow_text = isset($_GET['eyebrow'])
    ? trim(wp_strip_all_tags(sanitize_text_field(wp_unslash($_GET['eyebrow']))))
    : 'I’m looking for';

if ($eyebrow_text === '') {
    $eyebrow_text = 'I’m looking for';
}

$movie_filter_keys = ['funny', 'scary', 'sadness', 'pacing'];
$selected_movie_filters = [];
$movie_meta_query = [];
$movie_tax_query = [];

foreach ($movie_categories as $movie_category) {
    if ($selected_category === $movie_category->slug) {
        $selected_category_exists = true;
        break;
    }
}

if (!$selected_category_exists) {
    $selected_category = '';
}

if ($selected_category !== '') {
    $movie_tax_query[] = [
        'taxonomy' => 'category',
        'field' => 'slug',
        'terms' => $selected_category,
    ];
}

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

$movies_query_args = [
    'post_type' => 'movies',
    'post_status' => 'publish',
    'posts_per_page' => 15,
    'orderby' => 'rand',
];

if ($movie_meta_query !== []) {
    $movies_query_args['posts_per_page'] = -1;
    $movies_query_args['orderby'] = 'title';
    $movies_query_args['order'] = 'ASC';
    $movies_query_args['meta_query'] = $movie_meta_query;
}

if ($movie_tax_query !== []) {
    $movies_query_args['posts_per_page'] = -1;
    $movies_query_args['orderby'] = 'title';
    $movies_query_args['order'] = 'ASC';
    $movies_query_args['tax_query'] = $movie_tax_query;
}

$movies_query = new WP_Query($movies_query_args);
?>

<main class="mx-auto max-w-[1000px] px-6 py-16 md:px-8 md:py-24">
  <?php get_template_part('header/site', 'header'); ?>

  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <article class="mt-[56px]">
        <?php
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

        <?php if (get_the_content() !== '') : ?>
          <div class="post-content mt-8">
            <?php the_content(); ?>
          </div>
        <?php endif; ?>

        <?php
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
