<?php

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

function movies_theme_get_featured_content(int $post_id): string
{
    if (!function_exists('get_field')) {
        return '';
    }

    return trim((string) get_field('featured_content', $post_id));
}

function movies_theme_get_featured_image_id(int $post_id): int
{
    if (!function_exists('get_field')) {
        return 0;
    }

    return (int) get_field('featured_image', $post_id);
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
