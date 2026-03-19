<?php
add_action('after_setup_theme', function (): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_post_type_support('post', 'excerpt');
    add_post_type_support('collection', 'excerpt');
});

add_action('init', function (): void {
    register_post_type('movies', [
        'labels' => [
            'name' => 'Movies',
            'singular_name' => 'Movie',
            'add_new' => 'Add Movie',
            'add_new_item' => 'Add New Movie',
            'edit_item' => 'Edit Movie',
            'new_item' => 'New Movie',
            'view_item' => 'View Movie',
            'view_items' => 'View Movies',
            'search_items' => 'Search Movies',
            'not_found' => 'No movies found',
            'not_found_in_trash' => 'No movies found in Trash',
            'all_items' => 'All Movies',
            'archives' => 'Movie Archives',
            'attributes' => 'Movie Attributes',
            'featured_image' => 'Poster',
            'set_featured_image' => 'Set poster',
            'remove_featured_image' => 'Remove poster',
            'use_featured_image' => 'Use as poster',
            'menu_name' => 'Movies',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-format-video',
        'rewrite' => [
            'slug' => 'movies',
        ],
        'taxonomies' => [
            'category',
            'post_tag',
        ],
        'supports' => [
            'title',
            'thumbnail',
            'revisions',
        ],
        'show_in_rest' => true,
    ]);

    register_post_type('collection', [
        'labels' => [
            'name' => 'Collections',
            'singular_name' => 'Collection',
            'add_new' => 'Add Collection',
            'add_new_item' => 'Add New Collection',
            'edit_item' => 'Edit Collection',
            'new_item' => 'New Collection',
            'view_item' => 'View Collection',
            'view_items' => 'View Collections',
            'search_items' => 'Search Collections',
            'not_found' => 'No collections found',
            'not_found_in_trash' => 'No collections found in Trash',
            'all_items' => 'All Collections',
            'archives' => 'Collection Archives',
            'attributes' => 'Collection Attributes',
            'menu_name' => 'Collections',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-screenoptions',
        'rewrite' => [
            'slug' => 'collections',
        ],
        'supports' => [
            'title',
            'editor',
            'excerpt',
            'revisions',
        ],
        'show_in_rest' => true,
    ]);
});

add_action('wp_enqueue_scripts', function (): void {
    $theme = wp_get_theme();
    $theme_version = $theme->get('Version');
    $theme_dir = get_template_directory();
    $theme_uri = get_template_directory_uri();
    $manifest_path = $theme_dir . '/dist/.vite/manifest.json';

    if (!file_exists($manifest_path)) {
        wp_enqueue_style(
            'movies-minimal-theme-style',
            get_stylesheet_uri(),
            [],
            $theme_version
        );

        return;
    }

    $manifest = json_decode(file_get_contents($manifest_path), true);
    $entry = $manifest['src/main.js'] ?? null;

    if (!$entry) {
        return;
    }

    if (!empty($entry['css'])) {
        foreach ($entry['css'] as $index => $css_file) {
            wp_enqueue_style(
                'movies-minimal-app-' . $index,
                $theme_uri . '/dist/' . $css_file,
                [],
                $theme_version
            );
        }
    }

    if (!empty($entry['file'])) {
        wp_enqueue_script(
            'movies-minimal-app',
            $theme_uri . '/dist/' . $entry['file'],
            [],
            $theme_version,
            true
        );
        wp_script_add_data('movies-minimal-app', 'type', 'module');
    }
});

add_filter('query_vars', function (array $vars): array {
    $vars[] = 'movie_category';

    return $vars;
});

add_action('pre_get_posts', function (WP_Query $query): void {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->is_home()) {
        $query->set('post_type', 'collection');
        $query->set('posts_per_page', -1);
    }

    if ($query->is_author()) {
        $query->set('post_type', 'movies');
        $query->set('posts_per_page', -1);

        $category_slug = sanitize_title((string) $query->get('movie_category'));

        if ($category_slug !== '') {
            $query->set('tax_query', [
                [
                    'taxonomy' => 'category',
                    'field' => 'slug',
                    'terms' => $category_slug,
                ],
            ]);
        }
    }
});

add_action('acf/init', function (): void {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key' => 'group_movies_minimal_post_intro',
        'title' => 'Post Intro',
        'fields' => [
            [
                'key' => 'field_movies_minimal_post_intro',
                'label' => 'Intro',
                'name' => 'intro',
                'type' => 'wysiwyg',
                'instructions' => 'Rich intro content for post listings.',
                'required' => 0,
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 0,
                'delay' => 0,
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'post',
                ],
            ],
        ],
        'position' => 'normal',
        'style' => 'default',
        'active' => true,
    ]);

    acf_add_local_field_group([
        'key' => 'group_movies_minimal_movie_details',
        'title' => 'Movie Details',
        'fields' => [
            [
                'key' => 'field_movies_minimal_movie_subtitle',
                'label' => 'Subtitle',
                'name' => 'subtitle',
                'type' => 'text',
                'instructions' => 'Secondary line shown with the movie title.',
                'required' => 0,
            ],
            [
                'key' => 'field_movies_minimal_movie_year',
                'label' => 'Year',
                'name' => 'year',
                'type' => 'number',
                'instructions' => 'Release year for the movie.',
                'required' => 0,
                'min' => 1888,
                'max' => 2100,
                'step' => 1,
            ],
            [
                'key' => 'field_movies_minimal_movie_runtime',
                'label' => 'Runtime',
                'name' => 'runtime',
                'type' => 'text',
                'instructions' => 'Example: 121 min',
                'required' => 0,
            ],
            [
                'key' => 'field_movies_minimal_movie_brief_synopsis',
                'label' => 'Brief Synopsis',
                'name' => 'brief_synopsis',
                'type' => 'textarea',
                'instructions' => 'Short synopsis shown in collections.',
                'required' => 0,
                'rows' => 4,
                'new_lines' => 'br',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'movies',
                ],
            ],
        ],
        'position' => 'normal',
        'style' => 'default',
        'active' => true,
    ]);

    acf_add_local_field_group([
        'key' => 'group_movies_minimal_collection_movies',
        'title' => 'Collection Movies',
        'fields' => [
            [
                'key' => 'field_movies_minimal_collection_movies',
                'label' => 'Movies',
                'name' => 'movies',
                'type' => 'relationship',
                'instructions' => 'Select the movies that belong to this collection.',
                'required' => 0,
                'post_type' => [
                    'movies',
                ],
                'filters' => [
                    'search',
                ],
                'elements' => [
                    'featured_image',
                ],
                'return_format' => 'id',
                'min' => 0,
                'max' => 0,
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'collection',
                ],
            ],
        ],
        'position' => 'normal',
        'style' => 'default',
        'active' => true,
    ]);
});

add_action('acf/input/admin_head', function (): void {
    ?>
    <style>
      .acf-field[data-name="intro"] .acf-editor-wrap iframe {
        min-height: 160px !important;
      }

      .acf-field[data-name="intro"] .acf-editor-wrap textarea.wp-editor-area {
        min-height: 160px !important;
      }
    </style>
    <?php
});

function movies_minimal_get_post_intro(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('intro', $post_id));
}

function movies_minimal_get_subtitle(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('subtitle', $post_id));
}

function movies_minimal_get_year(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('year', $post_id));
}

function movies_minimal_get_runtime(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('runtime', $post_id));
}

function movies_minimal_get_brief_synopsis(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('brief_synopsis', $post_id));
}

function movies_minimal_get_collection_movies(int $post_id): array
{
    if (!function_exists('get_field')) {
        return [];
    }

    $movies = get_field('movies', $post_id);

    if (!is_array($movies)) {
        return [];
    }

    return array_values(array_filter(array_map('intval', $movies)));
}

function movies_minimal_get_movie_category_list(int $post_id): string
{
    $terms = get_the_terms($post_id, 'category');

    if (is_wp_error($terms) || $terms === false) {
        return '';
    }

    $names = array_map(static function (WP_Term $term): string {
        return $term->name;
    }, $terms);

    return implode(', ', $names);
}

function movies_minimal_get_list_summary(int $post_id): string
{
    $excerpt = trim(get_the_excerpt($post_id));

    if ($excerpt !== '') {
        return $excerpt;
    }

    return movies_minimal_get_post_intro($post_id);
}

function movies_minimal_get_author_movie_categories(int $author_id, int $limit = 4): array
{
    $movie_ids = get_posts([
        'post_type' => 'movies',
        'author' => $author_id,
        'posts_per_page' => -1,
        'fields' => 'ids',
        'no_found_rows' => true,
    ]);

    if ($movie_ids === []) {
        return [];
    }

    $terms = wp_get_object_terms($movie_ids, 'category', [
        'orderby' => 'name',
        'order' => 'ASC',
    ]);

    if (is_wp_error($terms) || $terms === []) {
        return [];
    }

    $unique_terms = [];

    foreach ($terms as $term) {
        $unique_terms[$term->term_id] = $term;
    }

    return array_slice(array_values($unique_terms), 0, $limit);
}

function movies_minimal_get_author_movie_category_stats(int $author_id, int $limit = 4): array
{
    $movie_ids = get_posts([
        'post_type' => 'movies',
        'author' => $author_id,
        'posts_per_page' => -1,
        'fields' => 'ids',
        'no_found_rows' => true,
    ]);

    if ($movie_ids === []) {
        return [
            'total_movies' => 0,
            'categories' => [],
        ];
    }

    $category_counts = [];

    foreach ($movie_ids as $movie_id) {
        $terms = get_the_terms($movie_id, 'category');

        if (is_wp_error($terms) || $terms === false) {
            continue;
        }

        foreach ($terms as $term) {
            if (!isset($category_counts[$term->term_id])) {
                $category_counts[$term->term_id] = [
                    'term' => $term,
                    'count' => 0,
                ];
            }

            $category_counts[$term->term_id]['count']++;
        }
    }

    if ($category_counts === []) {
        return [
            'total_movies' => count($movie_ids),
            'categories' => [],
        ];
    }

    uasort($category_counts, function (array $left, array $right): int {
        if ($left['count'] === $right['count']) {
            return strcmp($left['term']->name, $right['term']->name);
        }

        return $right['count'] <=> $left['count'];
    });

    $stats = [];
    $total_movies = count($movie_ids);

    foreach (array_slice($category_counts, 0, $limit, true) as $category_data) {
        $stats[] = [
            'term' => $category_data['term'],
            'count' => $category_data['count'],
            'percentage' => (int) round(($category_data['count'] / $total_movies) * 100),
        ];
    }

    return [
        'total_movies' => $total_movies,
        'categories' => $stats,
    ];
}
