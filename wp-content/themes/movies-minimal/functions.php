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

    register_post_type('home_intro', [
        'labels' => [
            'name' => 'Home Intros',
            'singular_name' => 'Home Intro',
            'add_new' => 'Add Home Intro',
            'add_new_item' => 'Add New Home Intro',
            'edit_item' => 'Edit Home Intro',
            'new_item' => 'New Home Intro',
            'view_item' => 'View Home Intro',
            'view_items' => 'View Home Intros',
            'search_items' => 'Search Home Intros',
            'not_found' => 'No home intros found',
            'not_found_in_trash' => 'No home intros found in Trash',
            'all_items' => 'All Home Intros',
            'menu_name' => 'Home Intro',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-edit-page',
        'supports' => [
            'title',
            'editor',
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
            'movies-theme-style',
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
                'movie-app-' . $index,
                $theme_uri . '/dist/' . $css_file,
                [],
                $theme_version
            );
        }
    }

    if (!empty($entry['file'])) {
        wp_enqueue_script(
            'movie-app',
            $theme_uri . '/dist/' . $entry['file'],
            [],
            $theme_version,
            true
        );
        wp_script_add_data('movie-app', 'type', 'module');
        wp_localize_script('movie-app', 'movieApp', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'heartNonce' => wp_create_nonce('movies_theme_toggle_heart'),
        ]);
    }
});

add_filter('option_aettaec_options', function ($options) {
    if (is_admin()) {
        return $options;
    }

    if (!is_array($options)) {
        $options = [];
    }

    $options['use_css'] = 0;

    return $options;
});

function movies_theme_get_inline_svg(string $relative_path, string $class_name = ''): string
{
    $svg_path = get_template_directory() . '/' . ltrim($relative_path, '/');

    if (!file_exists($svg_path)) {
        return '';
    }

    $svg = file_get_contents($svg_path);

    if (!is_string($svg) || $svg === '') {
        return '';
    }

    $class_attribute = trim('theme-logo ' . $class_name);

    return preg_replace(
        '/<svg\b([^>]*)>/',
        '<svg$1 class="' . esc_attr($class_attribute) . '" aria-hidden="true" focusable="false">',
        $svg,
        1
    ) ?? '';
}

add_action('init', function (): void {
    if (is_admin() || wp_doing_ajax()) {
        return;
    }

    if (isset($_COOKIE['movies_theme_visitor'])) {
        return;
    }

    $visitor_id = wp_generate_uuid4();
    $expires = time() + YEAR_IN_SECONDS;

    $_COOKIE['movies_theme_visitor'] = $visitor_id;

    setcookie('movies_theme_visitor', $visitor_id, [
        'expires' => $expires,
        'path' => COOKIEPATH ?: '/',
        'domain' => COOKIE_DOMAIN ?: '',
        'secure' => is_ssl(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
});

function movies_theme_get_visitor_id(): string
{
    return isset($_COOKIE['movies_theme_visitor'])
        ? sanitize_text_field(wp_unslash($_COOKIE['movies_theme_visitor']))
        : '';
}

function movies_theme_get_visitor_hash(): string
{
    $visitor_id = movies_theme_get_visitor_id();

    if ($visitor_id === '') {
        return '';
    }

    return hash_hmac('sha256', $visitor_id, wp_salt('auth'));
}

function movies_theme_get_collection_heart_hashes(int $post_id): array
{
    $hashes = get_post_meta($post_id, '_movies_theme_heart_hashes', true);

    if (!is_array($hashes)) {
        return [];
    }

    return array_values(array_filter(array_map('strval', $hashes)));
}

function movies_theme_get_collection_starting_hearts(int $post_id): int
{
    $starting_hearts = function_exists('get_field')
        ? get_field('starting_hearts', $post_id)
        : get_post_meta($post_id, 'starting_hearts', true);

    return max(0, (int) $starting_hearts);
}

function movies_theme_get_collection_heart_count(int $post_id): int
{
    return movies_theme_get_collection_starting_hearts($post_id)
        + count(movies_theme_get_collection_heart_hashes($post_id));
}

function movies_theme_collection_is_liked_by_current_visitor(int $post_id): bool
{
    $visitor_hash = movies_theme_get_visitor_hash();

    if ($visitor_hash === '') {
        return false;
    }

    return in_array($visitor_hash, movies_theme_get_collection_heart_hashes($post_id), true);
}

function movies_theme_update_collection_heart_state(int $post_id, bool $should_like): array
{
    $hashes = movies_theme_get_collection_heart_hashes($post_id);
    $visitor_hash = movies_theme_get_visitor_hash();

    if ($visitor_hash === '') {
        return [
            'liked' => false,
            'count' => movies_theme_get_collection_heart_count($post_id),
        ];
    }

    $already_liked = in_array($visitor_hash, $hashes, true);

    if ($should_like && !$already_liked) {
        $hashes[] = $visitor_hash;
    }

    if (!$should_like && $already_liked) {
        $hashes = array_values(array_filter(
            $hashes,
            static fn(string $hash): bool => $hash !== $visitor_hash
        ));
    }

    update_post_meta($post_id, '_movies_theme_heart_hashes', $hashes);
    update_post_meta($post_id, '_movies_theme_heart_count', count($hashes));

    return [
        'liked' => in_array($visitor_hash, $hashes, true),
        'count' => movies_theme_get_collection_heart_count($post_id),
    ];
}

function movies_theme_handle_collection_heart_ajax(): void
{
    check_ajax_referer('movies_theme_toggle_heart', 'nonce');

    $post_id = isset($_POST['postId']) ? absint($_POST['postId']) : 0;
    $liked = isset($_POST['liked']) && wp_unslash($_POST['liked']) === '1';

    if ($post_id < 1 || get_post_type($post_id) !== 'collection') {
        wp_send_json_error([
            'message' => __('Invalid collection.', 'movies-theme'),
        ], 400);
    }

    $result = movies_theme_update_collection_heart_state($post_id, $liked);

    wp_send_json_success($result);
}

add_action('wp_ajax_movies_theme_toggle_heart', 'movies_theme_handle_collection_heart_ajax');
add_action('wp_ajax_nopriv_movies_theme_toggle_heart', 'movies_theme_handle_collection_heart_ajax');

add_filter('query_vars', function (array $vars): array {
    $vars[] = 'movie_category';
    $vars[] = 'view';

    return $vars;
});

add_action('pre_get_posts', function (WP_Query $query): void {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->is_home()) {
        $query->set('post_type', 'collection');
        $query->set('posts_per_page', -1);

        if ((string) $query->get('view') === 'past-months') {
            $latest_collection = get_posts([
                'post_type' => 'collection',
                'posts_per_page' => 1,
                'fields' => 'ids',
                'no_found_rows' => true,
            ]);

            if ($latest_collection !== []) {
                $query->set('post__not_in', [(int) $latest_collection[0]]);
            }
        }
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
        'key' => 'group_movies_theme_post_intro',
        'title' => 'Post Intro',
        'fields' => [
            [
                'key' => 'field_movies_theme_post_intro',
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
        'key' => 'group_movies_theme_movie_details',
        'title' => 'Movie Details',
        'fields' => [
            [
                'key' => 'field_movies_theme_movie_subtitle',
                'label' => 'Subtitle',
                'name' => 'subtitle',
                'type' => 'text',
                'instructions' => 'Secondary line shown with the movie title.',
                'required' => 0,
            ],
            [
                'key' => 'field_movies_theme_movie_year',
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
                'key' => 'field_movies_theme_movie_runtime',
                'label' => 'Runtime',
                'name' => 'runtime',
                'type' => 'text',
                'instructions' => 'Example: 121 min',
                'required' => 0,
            ],
            [
                'key' => 'field_movies_theme_movie_brief_synopsis',
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
        'key' => 'group_movies_theme_collection_movies',
        'title' => 'Collection Movies',
        'fields' => [
            [
                'key' => 'field_movies_theme_collection_starting_hearts',
                'label' => 'Starting Hearts',
                'name' => 'starting_hearts',
                'type' => 'number',
                'instructions' => 'Optional starting heart count shown before new visitor hearts are added.',
                'required' => 0,
                'default_value' => 0,
                'min' => 0,
                'step' => 1,
            ],
            [
                'key' => 'field_movies_theme_collection_movies',
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

    acf_add_local_field_group([
        'key' => 'group_movies_theme_home_intro_meta',
        'title' => 'Home Intro Meta',
        'fields' => [
            [
                'key' => 'field_movies_theme_home_intro_link',
                'label' => 'Quotation Link',
                'name' => 'quotation_link',
                'type' => 'url',
                'instructions' => 'Optional link used for the quotation name shown under the intro.',
                'required' => 0,
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'home_intro',
                ],
            ],
        ],
        'position' => 'side',
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

function movies_theme_get_post_intro(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('intro', $post_id));
}

function movies_theme_get_subtitle(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('subtitle', $post_id));
}

function movies_theme_get_year(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('year', $post_id));
}

function movies_theme_get_runtime(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('runtime', $post_id));
}

function movies_theme_get_brief_synopsis(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('brief_synopsis', $post_id));
}

function movies_theme_get_collection_movies(int $post_id): array
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

function movies_theme_get_movie_category_list(int $post_id): string
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

function movies_theme_get_list_summary(int $post_id): string
{
    $excerpt = trim(get_the_excerpt($post_id));

    if ($excerpt !== '') {
        return $excerpt;
    }

    return movies_theme_get_post_intro($post_id);
}

function movies_theme_get_home_intro(): ?WP_Post
{
    $posts = get_posts([
        'post_type' => 'home_intro',
        'posts_per_page' => 1,
        'post_status' => 'publish',
        'no_found_rows' => true,
    ]);

    if ($posts === []) {
        return null;
    }

    return $posts[0];
}

function movies_theme_get_home_intro_link(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('quotation_link', $post_id));
}

function movies_theme_get_author_movie_categories(int $author_id, int $limit = 4): array
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

function movies_theme_get_author_movie_category_stats(int $author_id, int $limit = 4): array
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
