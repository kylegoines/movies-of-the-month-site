<?php

function movies_theme_get_site_title_marquee_state(): array
{
    $paused_cookie = isset($_COOKIE['movies_site_title_marquee_paused'])
        ? sanitize_text_field(wp_unslash($_COOKIE['movies_site_title_marquee_paused']))
        : '';
    $progress_cookie = isset($_COOKIE['movies_site_title_marquee_progress'])
        ? sanitize_text_field(wp_unslash($_COOKIE['movies_site_title_marquee_progress']))
        : '';
    $is_paused = $paused_cookie === '1';
    $progress = is_numeric($progress_cookie) ? (float) $progress_cookie : 0.0;
    $progress = max(0.0, min(1.0, $progress));

    return [
        'is_paused' => $is_paused,
        'progress' => $progress,
    ];
}

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

function movies_theme_get_post_intro(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('intro', $post_id));
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

    $runtime = trim((string) get_field('runtime', $post_id));

    if ($runtime === '') {
        return '';
    }

    if (ctype_digit($runtime)) {
        return $runtime . ' min';
    }

    if (preg_match('/\d+/', $runtime, $matches) === 1) {
        return $matches[0] . ' min';
    }

    return $runtime;
}

function movies_theme_get_the_pitch(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('the_pitch', $post_id));
}

function movies_theme_get_collection_quotes_heading(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('collection_quotes_heading', $post_id));
}

function movies_theme_get_signup_social_url(string $network): string
{
    $allowed_networks = [
        'twitter',
        'bluesky',
    ];

    if (!in_array($network, $allowed_networks, true)) {
        return '';
    }

    $field_name = 'signup_' . $network . '_url';

    if (function_exists('get_field')) {
        $option_value = get_field($field_name, 'option');

        if (is_string($option_value) && trim($option_value) !== '') {
            return trim($option_value);
        }

        $options_value = get_field($field_name, 'options');

        if (is_string($options_value) && trim($options_value) !== '') {
            return trim($options_value);
        }
    }

    $raw_option_value = get_option('options_' . $field_name);

    if (is_string($raw_option_value) && trim($raw_option_value) !== '') {
        return trim($raw_option_value);
    }

    $plain_option_value = get_option($field_name);

    return is_string($plain_option_value) ? trim($plain_option_value) : '';
}

function movies_theme_get_theme_setting(string $field_name)
{
    if (function_exists('get_field')) {
        $option_value = get_field($field_name, 'option');

        if ($option_value !== null) {
            return $option_value;
        }

        $options_value = get_field($field_name, 'options');

        if ($options_value !== null) {
            return $options_value;
        }
    }

    $raw_option_value = get_option('options_' . $field_name, null);

    if ($raw_option_value !== null) {
        return $raw_option_value;
    }

    return get_option($field_name, null);
}

function movies_theme_get_featured_content(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('featured_content', $post_id));
}

function movies_theme_get_feature_title(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('feature_title', $post_id));
}

function movies_theme_get_feature_byline_label(int $post_id): string
{
    if (!function_exists('get_field')) {
        return 'Thoughts by';
    }

    $label = trim((string) get_field('feature_byline_label', $post_id));

    return $label !== '' ? $label : 'Thoughts by';
}

function movies_theme_get_editorial_author_id(int $post_id): int
{
    if (!function_exists('get_field')) {
        return 0;
    }

    return (int) get_field('editorial_author', $post_id);
}

function movies_theme_get_editorial_article_count(int $author_id): int
{
    $movie_ids = get_posts([
        'post_type' => 'movies',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'no_found_rows' => true,
        'meta_query' => [
            [
                'key' => 'editorial_author',
                'value' => (string) $author_id,
                'compare' => '=',
            ],
        ],
    ]);

    return is_array($movie_ids) ? count($movie_ids) : 0;
}

function movies_theme_get_spotlight_quote(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('spotlight_quote', $post_id));
}

function movies_theme_get_pullquote_1(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('pullquote_1', $post_id));
}

function movies_theme_get_pullquote_2(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('pullquote_2', $post_id));
}

function movies_theme_get_pullquote_3(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('pullquote_3', $post_id));
}

function movies_theme_get_pullquote_position(int $post_id, int $index): string
{
    if (!function_exists('get_field')) {
        return 'position_1';
    }

    $position = trim((string) get_field('pullquote_position_' . $index, $post_id));
    $allowed_positions = ['position_1', 'position_2', 'position_3'];

    return in_array($position, $allowed_positions, true) ? $position : 'position_1';
}

function movies_theme_get_featured_image_id(int $post_id): int
{
    if (!function_exists('get_field')) {
        return 0;
    }

    return (int) get_field('featured_image', $post_id);
}

function movies_theme_should_add_poster_border(int $post_id): bool
{
    if (!function_exists('get_field')) {
        return false;
    }

    return (bool) get_field('add_border_to_poster', $post_id);
}

function movies_theme_get_poster_image_class(int $post_id, string $base_classes): string
{
    $classes = trim($base_classes);

    if (movies_theme_should_add_poster_border($post_id)) {
        $classes .= ' poster-image--bordered';
    }

    return trim($classes);
}

function movies_theme_is_hidden_gem(int $post_id): bool
{
    if (!function_exists('get_field')) {
        return false;
    }

    return (bool) get_field('hidden_gem', $post_id);
}

function movies_theme_get_collection_movies(int $post_id): array
{
    $entries = movies_theme_get_collection_movie_entries($post_id);

    return array_values(array_map(
        static fn(array $entry): int => (int) $entry['movie_id'],
        $entries
    ));
}

function movies_theme_get_related_movies(int $post_id): array
{
    if (!function_exists('get_field')) {
        return [];
    }

    $movies = get_field('related_movies', $post_id);

    if (!is_array($movies)) {
        return [];
    }

    return array_values(array_filter(array_map('intval', $movies)));
}

function movies_theme_get_collection_movie_entries(int $post_id): array
{
    if (!function_exists('get_field')) {
        return [];
    }

    $rows = get_field('collection_movie_entries', $post_id);

    if (!is_array($rows)) {
        return [];
    }

    $entries = [];

    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }

        $movie_field = $row['movie'] ?? null;
        $movie_id = 0;

        if (is_array($movie_field)) {
            $movie_id = (int) ($movie_field[0] ?? 0);
        } else {
            $movie_id = (int) $movie_field;
        }

        if ($movie_id <= 0) {
            continue;
        }

        $quotes = [];
        $quote_rows = $row['quotes'] ?? [];

        if (is_array($quote_rows)) {
            foreach ($quote_rows as $quote_row) {
                if (!is_array($quote_row)) {
                    continue;
                }

                $quote = trim((string) ($quote_row['quote'] ?? ''));
                $author_id = (int) ($quote_row['author'] ?? 0);

                if ($quote === '') {
                    continue;
                }

                $quotes[] = [
                    'quote' => $quote,
                    'author_id' => $author_id,
                ];
            }
        }

        $entries[] = [
            'movie_id' => $movie_id,
            'quotes' => $quotes,
        ];
    }

    return $entries;
}

function movies_theme_get_collection_description(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('collection_description', $post_id));
}

function movies_theme_get_category_color(int $term_id): string
{
    $color = sanitize_hex_color((string) get_term_meta($term_id, 'movies_theme_color', true));

    return is_string($color) ? $color : '';
}

function movies_theme_should_invert_category_chip_text(int $term_id): bool
{
    return get_term_meta($term_id, 'movies_theme_invert_chip_text', true) === '1';
}

function movies_theme_get_contrasting_text_color(string $hex_color): string
{
    $normalized = ltrim(trim($hex_color), '#');

    if (!preg_match('/^[0-9a-fA-F]{6}$/', $normalized)) {
        return '#111111';
    }

    $red = hexdec(substr($normalized, 0, 2));
    $green = hexdec(substr($normalized, 2, 2));
    $blue = hexdec(substr($normalized, 4, 2));
    $luminance = (($red * 299) + ($green * 587) + ($blue * 114)) / 1000;

    return $luminance >= 160 ? '#111111' : '#ffffff';
}

function movies_theme_get_collection_genres(int $post_id): array
{
    $movie_ids = movies_theme_get_collection_movies($post_id);

    if ($movie_ids === []) {
        return [];
    }

    $genres = [];

    foreach ($movie_ids as $movie_id) {
        $terms = get_the_terms($movie_id, 'category');

        if (is_wp_error($terms) || $terms === false) {
            continue;
        }

        foreach ($terms as $term) {
            $term_id = (int) $term->term_id;

            if (!isset($genres[$term_id])) {
                $background_color = movies_theme_get_category_color($term_id);
                $text_color = $background_color !== '' ? movies_theme_get_contrasting_text_color($background_color) : '';

                if ($text_color !== '' && movies_theme_should_invert_category_chip_text($term_id)) {
                    $text_color = $text_color === '#111111' ? '#ffffff' : '#111111';
                }

                $genres[$term_id] = [
                    'term_id' => $term_id,
                    'name' => $term->name,
                    'background_color' => $background_color,
                    'text_color' => $text_color,
                    'count' => 0,
                ];
            }

            $genres[$term_id]['count']++;
        }
    }

    uasort($genres, static function (array $left, array $right): int {
        if ($left['count'] === $right['count']) {
            return strcasecmp($left['name'], $right['name']);
        }

        return $right['count'] <=> $left['count'];
    });

    return array_values(array_slice($genres, 0, 6));
}

function movies_theme_get_hidden_gem_label_markup(string $class_name = ''): string
{
    $tooltip_message = 'Aha! What a treasure you found. This is a hidden gem: a well-loved recommendation off the beaten path. You should be proud of yourself.';
    $classes = trim('hidden-gem-tooltip ' . $class_name);

    return sprintf(
        '<span class="%1$s" tabindex="0"><span class="hidden-gem-tooltip__label">Hidden Gem</span><span class="hidden-gem-tooltip__popup" role="tooltip">%2$s</span></span>',
        esc_attr($classes),
        esc_html($tooltip_message)
    );
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

function movies_theme_get_movie_scale_value_label(int $post_id, string $field_name): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    $scale_label_config = movies_theme_get_scale_label_config();

    if (!isset($scale_label_config[$field_name])) {
        return '';
    }

    $value = (string) get_field($field_name, $post_id);

    if ($value === '') {
        return '';
    }

    return (string) ($scale_label_config[$field_name][$value] ?? '');
}

function movies_theme_get_list_summary(int $post_id): string
{
    if (get_post_type($post_id) === 'collection') {
        return movies_theme_get_collection_description($post_id);
    }

    return movies_theme_get_post_intro($post_id);
}

function movies_theme_get_featured_movie(): ?WP_Post
{
    if (!function_exists('get_field')) {
        return null;
    }

    $movies = get_posts([
        'post_type' => 'movies',
        'posts_per_page' => 1,
        'post_status' => 'publish',
        'meta_key' => 'featured_movie',
        'meta_value' => '1',
        'orderby' => 'date',
        'order' => 'DESC',
        'no_found_rows' => true,
    ]);

    if ($movies === []) {
        return null;
    }

    return $movies[0];
}

function movies_theme_get_recent_movie_activity(int $cluster_limit = 6, int $post_limit = 36): array
{
    $movies = get_posts([
        'post_type' => 'movies',
        'post_status' => 'publish',
        'posts_per_page' => $post_limit,
        'orderby' => 'date',
        'order' => 'DESC',
        'no_found_rows' => true,
    ]);

    if ($movies === []) {
        $clusters = [];
    } else {
        $clusters = [];
        $current_cluster = null;

        foreach ($movies as $movie) {
            if (!$movie instanceof WP_Post) {
                continue;
            }

            $author_id = (int) $movie->post_author;
            $timestamp = get_post_time('U', true, $movie);

            if ($current_cluster === null || $current_cluster['author_id'] !== $author_id) {
                if ($current_cluster !== null) {
                    $clusters[] = $current_cluster;
                }

                $current_cluster = [
                    'type' => 'movies',
                    'author_id' => $author_id,
                    'author_name' => movies_theme_get_author_name($author_id),
                    'count' => 0,
                    'movie_ids' => [],
                    'timestamp' => $timestamp,
                ];
            }

            $current_cluster['count']++;
            $current_cluster['movie_ids'][] = (int) $movie->ID;
            $current_cluster['timestamp'] = max($current_cluster['timestamp'], $timestamp);
        }

        if ($current_cluster !== null) {
            $clusters[] = $current_cluster;
        }
    }

    $editorial_movies = get_posts([
        'post_type' => 'movies',
        'post_status' => 'publish',
        'posts_per_page' => $post_limit,
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
        'meta_key' => '_movies_theme_editorial_logged_at',
        'meta_query' => [
            [
                'key' => '_movies_theme_editorial_logged_at',
                'compare' => 'EXISTS',
            ],
        ],
        'no_found_rows' => true,
    ]);

    $editorial_events = array_values(array_filter(array_map(static function ($movie): ?array {
        if (!$movie instanceof WP_Post) {
            return null;
        }

        $timestamp = (int) get_post_meta($movie->ID, '_movies_theme_editorial_logged_at', true);
        $author_id = movies_theme_get_editorial_author_id((int) $movie->ID);

        if ($author_id <= 0) {
            $author_id = (int) get_post_meta($movie->ID, '_movies_theme_editorial_logged_author', true);
        }

        if ($author_id <= 0) {
            $author_id = (int) $movie->post_author;
        }

        if ($timestamp <= 0 || $author_id <= 0) {
            return null;
        }

        return [
            'type' => 'editorial',
            'author_id' => $author_id,
            'author_name' => movies_theme_get_author_name($author_id),
            'movie_id' => (int) $movie->ID,
            'movie_title' => get_the_title($movie),
            'timestamp' => $timestamp,
        ];
    }, $editorial_movies)));

    $activity_items = array_merge($clusters, $editorial_events);

    if ($activity_items === []) {
        return [];
    }

    usort($activity_items, static function (array $left, array $right): int {
        return ((int) ($right['timestamp'] ?? 0)) <=> ((int) ($left['timestamp'] ?? 0));
    });

    $activity_items = array_slice($activity_items, 0, $cluster_limit);

    return array_values(array_map(static function (array $item): array {
        if (($item['type'] ?? 'movies') === 'editorial') {
            return [
                'type' => 'editorial',
                'author_id' => (int) $item['author_id'],
                'author_name' => (string) $item['author_name'],
                'movie_id' => (int) ($item['movie_id'] ?? 0),
                'movie_title' => (string) ($item['movie_title'] ?? ''),
            ];
        }

        return [
            'type' => 'movies',
            'author_id' => (int) $item['author_id'],
            'author_name' => (string) $item['author_name'],
            'count' => (int) ($item['count'] ?? 0),
            'movie_ids' => array_values(array_slice(array_map('intval', $item['movie_ids'] ?? []), 0, 5)),
        ];
    }, $activity_items));
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

function movies_theme_get_author_name(int $author_id): string
{
    return trim((string) get_the_author_meta('nickname', $author_id));
}

function movies_theme_is_author_visible_on_contributors_page(int $author_id): bool
{
    if (!function_exists('get_field')) {
        return false;
    }

    return (bool) get_field('show_on_contributors_page', 'user_' . $author_id);
}

function movies_theme_is_author_visible_on_browse_filter(int $author_id): bool
{
    if (!function_exists('get_field')) {
        return false;
    }

    return (bool) get_field('show_on_browse_filter', 'user_' . $author_id);
}

function movies_theme_get_visible_movie_authors_for_browse_filter(): array
{
    $authors = get_users([
        'fields' => 'all',
        'has_published_posts' => [
            'movies',
        ],
    ]);

    return array_values(array_filter($authors, static function (WP_User $user): bool {
        return movies_theme_is_author_visible_on_browse_filter((int) $user->ID);
    }));
}

function movies_theme_get_visible_movie_authors_for_contributors_page(): array
{
    $authors = get_users([
        'fields' => 'all',
    ]);

    return array_values(array_filter($authors, static function (WP_User $user): bool {
        return movies_theme_is_author_visible_on_contributors_page((int) $user->ID);
    }));
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
