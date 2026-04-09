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
    if (!function_exists('get_field')) {
        return [];
    }

    $movies = get_field('movies', $post_id);

    if (!is_array($movies)) {
        return [];
    }

    return array_values(array_filter(array_map('intval', $movies)));
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
    $first_name = trim((string) get_the_author_meta('first_name', $author_id));
    $last_name = trim((string) get_the_author_meta('last_name', $author_id));
    $full_name = trim($first_name . ' ' . $last_name);

    if ($full_name !== '') {
        return $full_name;
    }

    $display_name = trim((string) get_the_author_meta('display_name', $author_id));

    if ($display_name !== '') {
        return $display_name;
    }

    return trim((string) get_the_author_meta('user_nicename', $author_id));
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
