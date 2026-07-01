<?php

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
        'capability_type' => ['movie', 'movies'],
        'map_meta_cap' => true,
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
        'capability_type' => ['collection', 'collections'],
        'map_meta_cap' => true,
        'rewrite' => [
            'slug' => 'collections',
        ],
        'supports' => [
            'title',
            'editor',
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

    register_post_type('mailing_signup', [
        'labels' => [
            'name' => 'Mailing List',
            'singular_name' => 'Mailing Signup',
            'menu_name' => 'Mailing List',
            'all_items' => 'All Signups',
            'add_new' => 'Add Signup',
            'add_new_item' => 'Add Mailing Signup',
            'edit_item' => 'Edit Mailing Signup',
            'new_item' => 'New Mailing Signup',
            'view_item' => 'View Mailing Signup',
            'view_items' => 'View Mailing Signups',
            'search_items' => 'Search Mailing Signups',
            'not_found' => 'No mailing signups found',
            'not_found_in_trash' => 'No mailing signups found in Trash',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-email-alt',
        'supports' => [
            'title',
        ],
        'show_in_rest' => false,
    ]);

    register_post_type('mailing_post', [
        'labels' => [
            'name' => 'Mailing List Posts',
            'singular_name' => 'Mailing List Post',
            'menu_name' => 'Mailing List Posts',
            'all_items' => 'Email Archive',
            'add_new' => 'Add Email',
            'add_new_item' => 'Add Mailing List Post',
            'edit_item' => 'Edit Mailing List Post',
            'new_item' => 'New Mailing List Post',
            'view_item' => 'View Mailing List Post',
            'view_items' => 'View Mailing List Posts',
            'search_items' => 'Search Mailing List Posts',
            'not_found' => 'No mailing list posts found',
            'not_found_in_trash' => 'No mailing list posts found in Trash',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-email',
        'supports' => [
            'title',
            'editor',
            'revisions',
        ],
        'show_in_rest' => true,
    ]);
});

add_filter('manage_edit-mailing_signup_columns', function (array $columns): array {
    return [
        'cb' => $columns['cb'] ?? '<input type="checkbox" />',
        'title' => 'Email',
        'date' => 'Date',
    ];
});

add_filter('manage_edit-mailing_post_columns', function (array $columns): array {
    return [
        'cb' => $columns['cb'] ?? '<input type="checkbox" />',
        'title' => 'Internal Title',
        'mailing_post_subject' => 'Email Subject',
        'mailing_post_sent' => 'Last Sent',
        'mailing_post_count' => 'Recipients',
        'date' => 'Date',
    ];
});

add_action('manage_mailing_post_posts_custom_column', function (string $column, int $post_id): void {
    if ($column === 'mailing_post_subject') {
        $subject = function_exists('get_field') ? get_field('mailing_post_subject', $post_id) : '';
        echo esc_html(is_string($subject) && trim($subject) !== '' ? trim($subject) : get_the_title($post_id));
        return;
    }

    if ($column === 'mailing_post_sent') {
        $sent_at = (int) get_post_meta($post_id, '_movies_theme_mailing_post_sent_at', true);
        echo $sent_at > 0 ? esc_html(wp_date('M j, Y g:i a', $sent_at)) : 'Not sent';
        return;
    }

    if ($column === 'mailing_post_count') {
        $sent_count = (int) get_post_meta($post_id, '_movies_theme_mailing_post_sent_count', true);
        echo esc_html((string) $sent_count);
    }
}, 10, 2);
