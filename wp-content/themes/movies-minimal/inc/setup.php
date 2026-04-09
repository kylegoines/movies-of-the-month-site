<?php

add_action('after_setup_theme', function (): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_post_type_support('post', 'excerpt');
});

add_action('init', function (): void {
    global $wp_rewrite;
    $wp_rewrite->author_base = 'contributors';
});

add_action('admin_menu', function (): void {
    remove_menu_page('edit.php');
    remove_menu_page('edit-comments.php');
    remove_menu_page('themes.php');
}, 999);

add_filter('custom_menu_order', '__return_true');

add_filter('menu_order', function (array $menu_order): array {
    $preferred_order = [
        'edit.php?post_type=movies',
        'edit.php?post_type=collection',
        'upload.php',
        'edit.php?post_type=home_intro',
    ];

    $ordered = [];

    foreach ($preferred_order as $menu_slug) {
        if (in_array($menu_slug, $menu_order, true)) {
            $ordered[] = $menu_slug;
        }
    }

    foreach ($menu_order as $menu_slug) {
        if (!in_array($menu_slug, $ordered, true)) {
            $ordered[] = $menu_slug;
        }
    }

    return $ordered;
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

add_action('admin_post_nopriv_movies_theme_contribution_form', 'movies_theme_handle_contribution_form');
add_action('admin_post_movies_theme_contribution_form', 'movies_theme_handle_contribution_form');

function movies_theme_handle_contribution_form(): void
{
    $redirect_to = isset($_POST['redirect_to'])
        ? esc_url_raw(wp_unslash($_POST['redirect_to']))
        : home_url('/');

    if (!wp_verify_nonce(
        isset($_POST['movies_theme_contribution_nonce']) ? sanitize_text_field(wp_unslash($_POST['movies_theme_contribution_nonce'])) : '',
        'movies_theme_contribution_form'
    )) {
        wp_safe_redirect(add_query_arg('signup_status', 'error', $redirect_to));
        exit;
    }

    $honeypot = isset($_POST['company']) ? trim((string) wp_unslash($_POST['company'])) : '';

    if ($honeypot !== '') {
        wp_safe_redirect(add_query_arg('signup_status', 'success', $redirect_to));
        exit;
    }

    $topic = isset($_POST['contribution_topic'])
        ? sanitize_text_field(wp_unslash($_POST['contribution_topic']))
        : '';
    $name = isset($_POST['contribution_name'])
        ? trim(sanitize_text_field(wp_unslash($_POST['contribution_name'])))
        : '';
    $message = isset($_POST['contribution_message'])
        ? trim(sanitize_textarea_field(wp_unslash($_POST['contribution_message'])))
        : '';
    $allowed_topics = [
        'write_for_site' => 'I want to be a contributor',
        'movie_recommendation' => 'Movie Recommendation',
        'movie_spotlight_request' => 'Movie Spotlight Request',
        'general_inquiry' => 'General Inquiry',
    ];

    if (!isset($allowed_topics[$topic]) || $message === '') {
        wp_safe_redirect(add_query_arg('signup_status', 'error', $redirect_to));
        exit;
    }

    $subject = sprintf('Movies of the Month form: %s', $allowed_topics[$topic]);
    $body = implode("\n\n", [
        'Topic: ' . $allowed_topics[$topic],
        'Name: ' . ($name !== '' ? $name : 'Not provided'),
        'Message:',
        $message,
    ]);
    $sent = wp_mail('nashafoster@gmail.com', $subject, $body, [
        'Content-Type: text/plain; charset=UTF-8',
    ]);

    wp_safe_redirect(add_query_arg('signup_status', $sent ? 'success' : 'error', $redirect_to));
    exit;
}
