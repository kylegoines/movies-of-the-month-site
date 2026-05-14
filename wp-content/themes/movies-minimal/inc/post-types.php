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
});
