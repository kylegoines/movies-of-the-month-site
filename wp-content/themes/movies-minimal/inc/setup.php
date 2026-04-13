<?php

add_action('after_setup_theme', function (): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_post_type_support('post', 'excerpt');
});

add_action('init', function (): void {
    remove_role('author');
    remove_role('subscriber');

    $administrator = get_role('administrator');
    $contributor = get_role('contributor');
    $core_contributor = get_role('core_contributor');
    $editor = get_role('editor');

    $full_movie_and_collection_caps = [
        'edit_movies',
        'read_movie',
        'delete_movie',
        'edit_movie',
        'edit_others_movies',
        'publish_movies',
        'read_private_movies',
        'delete_movies',
        'delete_private_movies',
        'delete_published_movies',
        'delete_others_movies',
        'edit_private_movies',
        'edit_published_movies',
        'create_movies',
        'edit_collections',
        'read_collection',
        'delete_collection',
        'edit_collection',
        'edit_others_collections',
        'publish_collections',
        'read_private_collections',
        'delete_collections',
        'delete_private_collections',
        'delete_published_collections',
        'delete_others_collections',
        'edit_private_collections',
        'edit_published_collections',
        'create_collections',
    ];

    $contributor_movie_caps = [
        'upload_files',
        'edit_movies',
        'delete_movies',
        'create_movies',
    ];

    $restricted_movie_and_collection_caps = [
        'publish_movies',
        'edit_published_movies',
        'delete_published_movies',
        'edit_others_movies',
        'delete_others_movies',
        'read_private_movies',
        'edit_private_movies',
        'delete_private_movies',
        'create_collections',
        'edit_collections',
        'edit_published_collections',
        'delete_collections',
        'delete_published_collections',
        'publish_collections',
        'edit_others_collections',
        'delete_others_collections',
        'read_private_collections',
        'edit_private_collections',
        'delete_private_collections',
    ];

    $core_contributor_movie_caps = array_merge($contributor_movie_caps, [
        'publish_movies',
        'edit_published_movies',
        'delete_published_movies',
    ]);

    if ($administrator) {
        foreach ($full_movie_and_collection_caps as $capability) {
            $administrator->add_cap($capability);
        }
    }

    if ($administrator && $editor) {
        foreach ($administrator->capabilities as $capability => $granted) {
            if ($granted) {
                $editor->add_cap($capability);
            } else {
                $editor->remove_cap($capability);
            }
        }

        foreach ([
            'activate_plugins',
            'delete_plugins',
            'edit_plugins',
            'install_plugins',
            'update_plugins',
            'manage_options',
        ] as $capability) {
            $editor->remove_cap($capability);
        }
    }

    if (!$core_contributor && $contributor) {
        $core_contributor = add_role(
            'core_contributor',
            'Core Contributor',
            $contributor->capabilities
        );
    }

    if ($contributor) {
        foreach ($contributor_movie_caps as $capability) {
            $contributor->add_cap($capability);
        }

        foreach ($restricted_movie_and_collection_caps as $capability) {
            $contributor->remove_cap($capability);
        }
    }

    if (!$core_contributor) {
        return;
    }

    foreach ($core_contributor_movie_caps as $capability) {
        $core_contributor->add_cap($capability);
    }

    foreach ([
        'edit_others_movies',
        'delete_others_movies',
        'read_private_movies',
        'edit_private_movies',
        'delete_private_movies',
        'create_collections',
        'edit_collections',
        'edit_published_collections',
        'delete_collections',
        'delete_published_collections',
        'publish_collections',
        'edit_others_collections',
        'delete_others_collections',
        'read_private_collections',
        'edit_private_collections',
        'delete_private_collections',
    ] as $capability) {
        $core_contributor->remove_cap($capability);
    }
}, 20);

add_action('init', function (): void {
    global $wp_rewrite;
    $wp_rewrite->author_base = 'contributors';
});

add_action('admin_menu', function (): void {
    $user = wp_get_current_user();

    if (!($user instanceof WP_User)) {
        return;
    }

    if ($user->roles === ['contributor']) {
        remove_menu_page('edit.php');
        remove_menu_page('edit-comments.php');
        remove_menu_page('edit.php?post_type=page');
        remove_menu_page('plugins.php');
        remove_menu_page('themes.php');
        remove_menu_page('tools.php');
        remove_menu_page('options-general.php');
        remove_menu_page('edit.php?post_type=acf-field-group');
        remove_menu_page('wp-mail-smtp');

        return;
    }

    if ($user->roles === ['editor']) {
        remove_menu_page('edit.php');
        remove_menu_page('edit-comments.php');
        remove_menu_page('plugins.php');
        remove_menu_page('themes.php');
        remove_menu_page('tools.php');
        remove_menu_page('options-general.php');
        remove_menu_page('edit.php?post_type=acf-field-group');
    }
}, 999);

add_filter('custom_menu_order', '__return_true');

add_filter('editable_roles', function (array $roles): array {
    $preferred_order = [
        'administrator',
        'editor',
        'core_contributor',
        'contributor',
    ];

    $ordered_roles = [];

    foreach ($preferred_order as $role_key) {
        if (isset($roles[$role_key])) {
            $ordered_roles[$role_key] = $roles[$role_key];
        }
    }

    foreach ($roles as $role_key => $role_config) {
        if (!isset($ordered_roles[$role_key])) {
            $ordered_roles[$role_key] = $role_config;
        }
    }

    return $ordered_roles;
});

add_filter('menu_order', function (array $menu_order): array {
    $user = wp_get_current_user();

    if (!($user instanceof WP_User)) {
        return $menu_order;
    }

    if ($user->roles === ['editor']) {
        $preferred_order = [
            'index.php',
            'edit.php?post_type=movies',
            'edit.php?post_type=collection',
            'users.php',
            'upload.php',
            'edit.php?post_type=page',
        ];

        $reordered = [];

        foreach ($preferred_order as $menu_slug) {
            if (in_array($menu_slug, $menu_order, true)) {
                $reordered[] = $menu_slug;
            }
        }

        foreach ($menu_order as $menu_slug) {
            if (!in_array($menu_slug, $reordered, true)) {
                $reordered[] = $menu_slug;
            }
        }

        return $reordered;
    }

    if ($user->roles !== ['contributor']) {
        return $menu_order;
    }

    $preferred_order = [
        'edit.php?post_type=movies',
        'edit.php?post_type=collection',
        'upload.php',
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

add_action('admin_head-profile.php', function (): void {
    ?>
    <style>
      .user-syntax-highlighting-wrap,
      .user-comment-shortcuts-wrap,
      .user-display-name-wrap {
        display: none !important;
      }
    </style>
    <?php
});

add_action('admin_head-user-edit.php', function (): void {
    ?>
    <style>
      .user-syntax-highlighting-wrap,
      .user-comment-shortcuts-wrap,
      .user-display-name-wrap {
        display: none !important;
      }
    </style>
    <?php
});

add_action('admin_footer-profile.php', 'movies_theme_render_nickname_note');
add_action('admin_footer-user-edit.php', 'movies_theme_render_nickname_note');

function movies_theme_render_nickname_note(): void
{
    ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const nicknameRow = document.querySelector('.user-nickname-wrap');

        if (!nicknameRow || nicknameRow.querySelector('.movies-theme-nickname-note')) {
          return;
        }

        const description = document.createElement('p');
        description.className = 'description movies-theme-nickname-note';
        description.textContent = 'This is the user-facing name shown with your posts, editorials, and recommendations.';

        const nicknameCell = nicknameRow.querySelector('td');

        if (nicknameCell) {
          nicknameCell.appendChild(description);
        }
      });
    </script>
    <?php
}

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
