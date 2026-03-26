<?php

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

function movies_theme_get_movie_heart_hashes(int $post_id): array
{
    $hashes = get_post_meta($post_id, '_movies_theme_heart_hashes', true);

    if (!is_array($hashes)) {
        return [];
    }

    return array_values(array_filter(array_map('strval', $hashes)));
}

function movies_theme_get_movie_starting_hearts(int $post_id): int
{
    $starting_hearts = function_exists('get_field')
        ? get_field('starting_hearts', $post_id)
        : get_post_meta($post_id, 'starting_hearts', true);

    return max(0, (int) $starting_hearts);
}

function movies_theme_get_movie_heart_count(int $post_id): int
{
    return movies_theme_get_movie_starting_hearts($post_id)
        + count(movies_theme_get_movie_heart_hashes($post_id));
}

function movies_theme_movie_is_liked_by_current_visitor(int $post_id): bool
{
    $visitor_hash = movies_theme_get_visitor_hash();

    if ($visitor_hash === '') {
        return false;
    }

    return in_array($visitor_hash, movies_theme_get_movie_heart_hashes($post_id), true);
}

function movies_theme_update_movie_heart_state(int $post_id, bool $should_like): array
{
    $hashes = movies_theme_get_movie_heart_hashes($post_id);
    $visitor_hash = movies_theme_get_visitor_hash();

    if ($visitor_hash === '') {
        return [
            'liked' => false,
            'count' => movies_theme_get_movie_heart_count($post_id),
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
        'count' => movies_theme_get_movie_heart_count($post_id),
    ];
}

function movies_theme_handle_movie_heart_ajax(): void
{
    check_ajax_referer('movies_theme_toggle_heart', 'nonce');

    $post_id = isset($_POST['postId']) ? absint($_POST['postId']) : 0;
    $liked = isset($_POST['liked']) && wp_unslash($_POST['liked']) === '1';

    if ($post_id < 1 || get_post_type($post_id) !== 'movies') {
        wp_send_json_error([
            'message' => __('Invalid movie.', 'movies-theme'),
        ], 400);
    }

    $result = movies_theme_update_movie_heart_state($post_id, $liked);

    wp_send_json_success($result);
}

add_action('wp_ajax_movies_theme_toggle_heart', 'movies_theme_handle_movie_heart_ajax');
add_action('wp_ajax_nopriv_movies_theme_toggle_heart', 'movies_theme_handle_movie_heart_ajax');
