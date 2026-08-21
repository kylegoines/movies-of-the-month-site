<?php

function movies_theme_user_is_contributor_only(): bool
{
    $user = wp_get_current_user();

    return $user instanceof WP_User && $user->roles === ['contributor'];
}

function movies_theme_get_edited_movie_fields(): array
{
    static $edited_fields_by_post = [];

    $post_id = isset($_GET['post']) ? (int) $_GET['post'] : 0;

    if ($post_id < 1 || get_post_type($post_id) !== 'movies') {
        return [];
    }

    if (!array_key_exists($post_id, $edited_fields_by_post)) {
        $edited_fields = get_post_meta($post_id, '_movies_theme_edited_fields', true);
        $edited_fields_by_post[$post_id] = is_array($edited_fields) ? $edited_fields : [];
    }

    return $edited_fields_by_post[$post_id];
}

function movies_theme_get_previous_movie_content(): array
{
    $post_id = isset($_GET['post']) ? (int) $_GET['post'] : 0;

    if ($post_id < 1 || get_post_type($post_id) !== 'movies') {
        return [];
    }

    $previous_content = get_post_meta($post_id, '_movies_theme_previous_content', true);

    if (!is_array($previous_content)) {
        return [];
    }

    $scale_label_config = movies_theme_get_scale_label_config();

    foreach ($previous_content as $field_name => $previous_value) {
        if (
            isset($scale_label_config[$field_name])
            && array_key_exists((string) $previous_value, $scale_label_config[$field_name])
        ) {
            $previous_content[$field_name] = $scale_label_config[$field_name][(string) $previous_value];
        }
    }

    return $previous_content;
}

add_filter('acf/prepare_field', function ($field) {
    if (
        !is_array($field)
        || empty($field['name'])
        || !current_user_can('edit_others_movies')
        || !in_array($field['name'], movies_theme_get_edited_movie_fields(), true)
    ) {
        return $field;
    }

    $field['label'] = rtrim((string) $field['label']) . ' (edited)';

    return $field;
});

add_filter('acf/prepare_field/key=field_movies_theme_movie_featured', function ($field) {
    if (movies_theme_user_is_contributor_only()) {
        return false;
    }

    return $field;
});

add_filter('acf/prepare_field/key=field_movies_theme_movie_starting_hearts', function ($field) {
    if (movies_theme_user_is_contributor_only()) {
        return false;
    }

    return $field;
});

add_filter('acf/update_value/key=field_movies_theme_movie_featured', function ($value, $post_id, $field) {
    if (!movies_theme_user_is_contributor_only()) {
        return $value;
    }

    return get_post_meta((int) $post_id, $field['name'], true);
}, 10, 3);

add_filter('acf/update_value/key=field_movies_theme_movie_starting_hearts', function ($value, $post_id, $field) {
    if (!movies_theme_user_is_contributor_only()) {
        return $value;
    }

    return get_post_meta((int) $post_id, $field['name'], true);
}, 10, 3);

add_action('acf/init', function (): void {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    if (function_exists('acf_add_options_page')) {
        acf_add_options_page([
            'page_title' => 'Theme Settings',
            'menu_title' => 'Theme Settings',
            'menu_slug' => 'movies-theme-settings',
            'capability' => 'edit_posts',
            'redirect' => false,
            'position' => 61,
        ]);
    }

    $scale_label_config = movies_theme_get_scale_label_config();

    acf_add_local_field_group([
        'key' => 'group_movies_theme_settings',
        'title' => 'Theme Settings',
        'fields' => [
            [
                'key' => 'field_movies_theme_settings_signup_twitter_url',
                'label' => 'Signup Twitter URL',
                'name' => 'signup_twitter_url',
                'type' => 'url',
                'instructions' => 'Optional Twitter/X URL used next to the Get In Touch label.',
                'required' => 0,
                'default_value' => '',
                'placeholder' => 'https://x.com/yourhandle',
            ],
            [
                'key' => 'field_movies_theme_settings_signup_bluesky_url',
                'label' => 'Signup Bluesky URL',
                'name' => 'signup_bluesky_url',
                'type' => 'url',
                'instructions' => 'Optional Bluesky URL used next to the Get In Touch label.',
                'required' => 0,
                'default_value' => '',
                'placeholder' => 'https://bsky.app/profile/yourhandle',
            ],
            [
                'key' => 'field_movies_theme_settings_mailing_confirmation_enabled',
                'label' => 'Send Mailing List Confirmation Email',
                'name' => 'mailing_confirmation_enabled',
                'type' => 'true_false',
                'instructions' => 'Send a confirmation email after someone joins the mailing list.',
                'required' => 0,
                'ui' => 1,
                'default_value' => 1,
            ],
            [
                'key' => 'field_movies_theme_settings_mailing_confirmation_subject',
                'label' => 'Mailing List Confirmation Subject',
                'name' => 'mailing_confirmation_subject',
                'type' => 'text',
                'instructions' => 'Subject line for the subscriber confirmation email.',
                'required' => 0,
                'default_value' => 'Thank you for subscribing to Movies of the Month!',
                'placeholder' => 'Thank you for subscribing to Movies of the Month!',
            ],
            [
                'key' => 'field_movies_theme_settings_mailing_confirmation_body',
                'label' => 'Mailing List Confirmation Body',
                'name' => 'mailing_confirmation_body',
                'type' => 'textarea',
                'instructions' => 'Plain text email body. You can use {email} to include the subscriber address.',
                'required' => 0,
                'default_value' => "Thank you for subscribing to Movies of the Month!\n\nWe'll keep you posted with fun updates as we grow.\n\nWe promise not to spam you, and we'll never sell your data.",
                'rows' => 8,
                'new_lines' => 'br',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'movies-theme-settings',
                ],
            ],
        ],
        'position' => 'normal',
        'style' => 'default',
        'active' => true,
    ]);

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
        'key' => 'group_movies_theme_mailing_post',
        'title' => 'Mailing List Email',
        'fields' => [
            [
                'key' => 'field_movies_theme_mailing_post_subject',
                'label' => 'Email Subject',
                'name' => 'mailing_post_subject',
                'type' => 'text',
                'instructions' => 'Subject line sent to everyone on the mailing list.',
                'required' => 1,
                'default_value' => '',
                'placeholder' => 'Movies of the Month Update',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'mailing_post',
                ],
            ],
        ],
        'position' => 'normal',
        'style' => 'default',
        'active' => true,
    ]);

    acf_add_local_field_group([
        'key' => 'group_movies_theme_showdown_details',
        'title' => 'Showdown Details',
        'fields' => [
            [
                'key' => 'field_movies_theme_showdown_progress_title',
                'label' => 'Progress Title',
                'name' => 'progress_title',
                'type' => 'text',
                'instructions' => 'Short progress/status line shown in the Showdown module.',
                'required' => 0,
                'default_value' => '',
                'placeholder' => 'Voting now open',
            ],
            [
                'key' => 'field_movies_theme_showdown_twitter_url',
                'label' => 'Twitter/X URL',
                'name' => 'twitter_url',
                'type' => 'url',
                'instructions' => 'Optional Twitter/X link for this Showdown.',
                'required' => 0,
                'default_value' => '',
                'placeholder' => 'https://x.com/yourhandle/status/...',
            ],
            [
                'key' => 'field_movies_theme_showdown_bluesky_url',
                'label' => 'Bluesky URL',
                'name' => 'bluesky_url',
                'type' => 'url',
                'instructions' => 'Optional Bluesky link for this Showdown.',
                'required' => 0,
                'default_value' => '',
                'placeholder' => 'https://bsky.app/profile/yourhandle/post/...',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'showdown',
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
                'key' => 'field_movies_theme_movie_funny',
                'label' => 'Funny',
                'name' => 'funny',
                'type' => 'select',
                'instructions' => 'How funny the movie is.',
                'required' => 0,
                'choices' => $scale_label_config['funny'],
                'default_value' => '0',
                'return_format' => 'value',
                'allow_null' => 0,
                'ui' => 0,
            ],
            [
                'key' => 'field_movies_theme_movie_scary',
                'label' => 'Scary',
                'name' => 'scary',
                'type' => 'select',
                'instructions' => 'How scary the movie is.',
                'required' => 0,
                'choices' => $scale_label_config['scary'],
                'default_value' => '0',
                'return_format' => 'value',
                'allow_null' => 0,
                'ui' => 0,
            ],
            [
                'key' => 'field_movies_theme_movie_sadness',
                'label' => 'Sadness',
                'name' => 'sadness',
                'type' => 'select',
                'instructions' => 'How sad the movie is.',
                'required' => 0,
                'choices' => $scale_label_config['sadness'],
                'default_value' => '0',
                'return_format' => 'value',
                'allow_null' => 0,
                'ui' => 0,
            ],
            [
                'key' => 'field_movies_theme_movie_pacing',
                'label' => 'Pacing',
                'name' => 'pacing',
                'type' => 'select',
                'instructions' => 'How quickly the movie moves.',
                'required' => 0,
                'choices' => $scale_label_config['pacing'],
                'default_value' => '0',
                'return_format' => 'value',
                'allow_null' => 0,
                'ui' => 0,
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
        'key' => 'group_movies_theme_movie_toggles',
        'title' => 'Movie Toggles',
        'fields' => [
            [
                'key' => 'field_movies_theme_movie_hidden_gem',
                'label' => 'Hidden Gem',
                'name' => 'hidden_gem',
                'type' => 'true_false',
                'instructions' => 'Add the hidden gem treatment to this movie.',
                'required' => 0,
                'ui' => 1,
                'default_value' => 0,
            ],
            [
                'key' => 'field_movies_theme_movie_featured',
                'label' => 'Spotlight Movie',
                'name' => 'featured_movie',
                'type' => 'true_false',
                'instructions' => 'Spotlight movie mode.',
                'required' => 0,
                'ui' => 1,
                'default_value' => 0,
            ],
            [
                'key' => 'field_movies_theme_movie_add_border_to_poster',
                'label' => 'Add Border to Poster',
                'name' => 'add_border_to_poster',
                'type' => 'true_false',
                'instructions' => 'If the poster is mainly white, enable this to help it be visible on a white background.',
                'required' => 0,
                'ui' => 1,
                'default_value' => 0,
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
        'key' => 'group_movies_theme_movie_hearts',
        'title' => 'Hearts',
        'fields' => [
            [
                'key' => 'field_movies_theme_movie_starting_hearts',
                'label' => 'Starting Hearts',
                'name' => 'starting_hearts',
                'type' => 'number',
                'instructions' => 'Optional starting heart count shown before new visitor hearts are added.',
                'required' => 0,
                'default_value' => 0,
                'min' => 0,
                'step' => 1,
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
        'position' => 'side',
        'style' => 'default',
        'active' => true,
    ]);

    acf_add_local_field_group([
        'key' => 'group_movies_theme_movie_featured_content',
        'title' => 'Editorial Content',
        'fields' => [
            [
                'key' => 'field_movies_theme_movie_featured_image',
                'label' => 'Editorial Image',
                'name' => 'featured_image',
                'type' => 'image',
                'instructions' => 'Optional image used for the editorial movie layout. If not provided, the poster will be used.',
                'required' => 0,
                'return_format' => 'id',
                'preview_size' => 'medium',
                'library' => 'all',
            ],
            [
                'key' => 'field_movies_theme_movie_feature_title',
                'label' => 'Editorial Title',
                'name' => 'feature_title',
                'type' => 'text',
                'instructions' => 'Optional title shown above the editorial post content.',
                'required' => 0,
                'default_value' => '',
                'placeholder' => '',
            ],
            [
                'key' => 'field_movies_theme_movie_feature_byline_label',
                'label' => 'Editorial Byline Label',
                'name' => 'feature_byline_label',
                'type' => 'text',
                'instructions' => 'Optional custom label shown before the author name. Example: Review by.',
                'required' => 0,
                'default_value' => 'Thoughts by',
                'placeholder' => '',
            ],
            [
                'key' => 'field_movies_theme_movie_editorial_author',
                'label' => 'Editorial Author Attribution',
                'name' => 'editorial_author',
                'type' => 'user',
                'instructions' => 'Select the author attribution for the editorial piece on this movie. This is the user-facing byline shown with the editorial content.',
                'required' => 0,
                'role' => [
                    'administrator',
                    'editor',
                    'core_contributor',
                    'contributor',
                ],
                'return_format' => 'id',
                'multiple' => 0,
                'allow_null' => 1,
            ],
            [
                'key' => 'field_movies_theme_movie_spotlight_quote_accordion',
                'label' => 'Spotlight Quote',
                'name' => '',
                'type' => 'accordion',
                'open' => 0,
                'multi_expand' => 0,
                'endpoint' => 0,
            ],
            [
                'key' => 'field_movies_theme_movie_spotlight_quote',
                'label' => 'Spotlight Quote',
                'name' => 'spotlight_quote',
                'type' => 'wysiwyg',
                'instructions' => 'Optional editorial spotlight quote. Use the Pullquote formatting dropdown to add the pink highlight style when needed.',
                'required' => 0,
                'tabs' => 'all',
                'toolbar' => 'pullquote',
                'media_upload' => 0,
                'delay' => 0,
            ],
            [
                'key' => 'field_movies_theme_movie_spotlight_quote_accordion_endpoint',
                'label' => '',
                'name' => '',
                'type' => 'accordion',
                'endpoint' => 1,
            ],
            [
                'key' => 'field_movies_theme_movie_featured_content',
                'label' => 'Editorial Content',
                'name' => 'featured_content',
                'type' => 'wysiwyg',
                'instructions' => 'Rich content shown in the editorial movie layout. Filling this out will change the movie post into editorial mode.',
                'required' => 0,
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 0,
                'delay' => 0,
            ],
            [
                'key' => 'field_movies_theme_movie_gallery',
                'label' => 'Spotlight Gallery',
                'name' => 'spotlight_gallery',
                'type' => 'gallery',
                'instructions' => 'Optional gallery flashed on hover in the homepage spotlight module.',
                'required' => 0,
                'return_format' => 'id',
                'preview_size' => 'medium',
                'insert' => 'append',
                'library' => 'all',
                'min' => 0,
                'max' => 0,
                'mime_types' => 'jpg,jpeg,png,webp,avif',
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
        'key' => 'group_movies_theme_movie_pullquotes',
        'title' => 'Pullquotes',
        'fields' => [
            [
                'key' => 'field_movies_theme_movie_pullquote_note',
                'label' => 'Content Editors Note',
                'name' => '',
                'type' => 'message',
                'message' => 'These pullquotes are for editorial flair. Keep them short lines, and use the Pullquote formatting dropdown to style them.',
                'new_lines' => 'wpautop',
                'esc_html' => 0,
            ],
            [
                'key' => 'field_movies_theme_movie_pullquote_1',
                'label' => 'Pullquote 1',
                'name' => 'pullquote_1',
                'type' => 'wysiwyg',
                'instructions' => 'Rich text pullquote for this movie.',
                'required' => 0,
                'tabs' => 'all',
                'toolbar' => 'pullquote',
                'media_upload' => 0,
                'delay' => 0,
            ],
            [
                'key' => 'field_movies_theme_movie_pullquote_position_1',
                'label' => 'Pullquote 1 Position',
                'name' => 'pullquote_position_1',
                'type' => 'select',
                'instructions' => 'Choose where Pullquote 1 should appear.',
                'required' => 0,
                'choices' => [
                    'position_1' => 'Position 1 (right of feature image)',
                    'position_2' => 'Position 2 (left of content)',
                    'position_3' => 'Position 3 (left of feature image)',
                ],
                'default_value' => 'position_1',
                'return_format' => 'value',
                'allow_null' => 0,
                'ui' => 0,
            ],
            [
                'key' => 'field_movies_theme_movie_pullquote_2',
                'label' => 'Pullquote 2',
                'name' => 'pullquote_2',
                'type' => 'wysiwyg',
                'instructions' => 'Rich text pullquote for this movie.',
                'required' => 0,
                'tabs' => 'all',
                'toolbar' => 'pullquote',
                'media_upload' => 0,
                'delay' => 0,
            ],
            [
                'key' => 'field_movies_theme_movie_pullquote_position_2',
                'label' => 'Pullquote 2 Position',
                'name' => 'pullquote_position_2',
                'type' => 'select',
                'instructions' => 'Choose where Pullquote 2 should appear.',
                'required' => 0,
                'choices' => [
                    'position_1' => 'Position 1 (right of feature image)',
                    'position_2' => 'Position 2 (left of content)',
                    'position_3' => 'Position 3 (left of feature image)',
                ],
                'default_value' => 'position_2',
                'return_format' => 'value',
                'allow_null' => 0,
                'ui' => 0,
            ],
            [
                'key' => 'field_movies_theme_movie_pullquote_3',
                'label' => 'Pullquote 3',
                'name' => 'pullquote_3',
                'type' => 'wysiwyg',
                'instructions' => 'Rich text pullquote for this movie.',
                'required' => 0,
                'tabs' => 'all',
                'toolbar' => 'pullquote',
                'media_upload' => 0,
                'delay' => 0,
            ],
            [
                'key' => 'field_movies_theme_movie_pullquote_position_3',
                'label' => 'Pullquote 3 Position',
                'name' => 'pullquote_position_3',
                'type' => 'select',
                'instructions' => 'Choose where Pullquote 3 should appear.',
                'required' => 0,
                'choices' => [
                    'position_1' => 'Position 1 (right of feature image)',
                    'position_2' => 'Position 2 (left of content)',
                    'position_3' => 'Position 3 (left of feature image)',
                ],
                'default_value' => 'position_3',
                'return_format' => 'value',
                'allow_null' => 0,
                'ui' => 0,
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
        'key' => 'group_movies_theme_movie_info',
        'title' => 'Movie Info',
        'fields' => [
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
                'type' => 'number',
                'instructions' => 'Runtime in minutes. Example: 121',
                'required' => 0,
                'min' => 1,
                'step' => 1,
            ],
            [
                'key' => 'field_movies_theme_movie_pitch',
                'label' => 'The Pitch',
                'name' => 'the_pitch',
                'type' => 'textarea',
                'instructions' => 'Describe the film in a one or two sentence pitch.',
                'required' => 0,
                'rows' => 3,
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
        'key' => 'group_movies_theme_movie_related',
        'title' => 'Related',
        'fields' => [
            [
                'key' => 'field_movies_theme_movie_related_movies',
                'label' => 'Related Movies',
                'name' => 'related_movies',
                'type' => 'relationship',
                'instructions' => 'Optional related movies shown on the standard single movie page above the More from author section.',
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
                'key' => 'field_movies_theme_collection_description',
                'label' => 'Collection Description',
                'name' => 'collection_description',
                'type' => 'textarea',
                'instructions' => 'Short description shown on the homepage collection list.',
                'required' => 0,
                'rows' => 3,
                'new_lines' => 'br',
            ],
            [
                'key' => 'field_movies_theme_collection_movie_entries',
                'label' => 'Collection Movie Entries',
                'name' => 'collection_movie_entries',
                'type' => 'repeater',
                'instructions' => 'Collection movie list for this page. Add optional quotes for each movie entry.',
                'required' => 0,
                'layout' => 'row',
                'collapsed' => 'field_movies_theme_collection_movie_entry_movie',
                'button_label' => 'Add Collection Movie',
                'sub_fields' => [
                    [
                        'key' => 'field_movies_theme_collection_movie_entry_movie',
                        'label' => 'Movie',
                        'name' => 'movie',
                        'type' => 'relationship',
                        'required' => 1,
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
                        'min' => 1,
                        'max' => 1,
                    ],
                    [
                        'key' => 'field_movies_theme_collection_movie_entry_quotes',
                        'label' => 'Quotes',
                        'name' => 'quotes',
                        'type' => 'repeater',
                        'instructions' => 'Optional quotes shown below the pitch on the collection page.',
                        'required' => 0,
                        'layout' => 'row',
                        'collapsed' => 'field_movies_theme_collection_movie_entry_quote_text',
                        'button_label' => 'Add Quote',
                        'sub_fields' => [
                            [
                                'key' => 'field_movies_theme_collection_movie_entry_quote_text',
                                'label' => 'Quote',
                                'name' => 'quote',
                                'type' => 'textarea',
                                'instructions' => 'Short quote for this movie entry.',
                                'required' => 0,
                                'rows' => 3,
                                'new_lines' => 'br',
                            ],
                            [
                                'key' => 'field_movies_theme_collection_movie_entry_quote_author',
                                'label' => 'Author',
                                'name' => 'author',
                                'type' => 'user',
                                'instructions' => 'Author attribution shown on this quote.',
                                'required' => 0,
                                'role' => [
                                    'administrator',
                                    'editor',
                                    'core_contributor',
                                    'contributor',
                                ],
                                'return_format' => 'id',
                                'multiple' => 0,
                                'allow_null' => 1,
                            ],
                        ],
                        'min' => 0,
                        'max' => 0,
                    ],
                ],
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
        'key' => 'group_movies_theme_author_profile',
        'title' => 'Author Profile',
        'fields' => [
            [
                'key' => 'field_movies_theme_author_image',
                'label' => 'Profile Image',
                'name' => 'profile_image',
                'type' => 'image',
                'instructions' => 'Optional author image for profile and byline areas.',
                'required' => 0,
                'return_format' => 'id',
                'preview_size' => 'medium',
                'library' => 'all',
            ],
            [
                'key' => 'field_movies_theme_author_visible_on_contributors_page',
                'label' => 'Show On Contributors Page',
                'name' => 'show_on_contributors_page',
                'type' => 'true_false',
                'instructions' => 'Enable this to show the user on the public Contributors page.',
                'required' => 0,
                'ui' => 1,
                'default_value' => 1,
            ],
            [
                'key' => 'field_movies_theme_author_visible_on_browse_filter',
                'label' => 'Show On Browse Filter',
                'name' => 'show_on_browse_filter',
                'type' => 'true_false',
                'instructions' => 'Enable this to show the user in the Browse page author dropdown filter.',
                'required' => 0,
                'ui' => 1,
                'default_value' => 1,
            ],
            [
                'key' => 'field_movies_theme_author_bio',
                'label' => 'Bio',
                'name' => 'bio',
                'type' => 'textarea',
                'instructions' => 'Optional custom bio. Falls back to the default WordPress biographical info if left empty.',
                'required' => 0,
                'rows' => 5,
                'new_lines' => 'br',
            ],
            [
                'key' => 'field_movies_theme_author_website',
                'label' => 'Personal Website',
                'name' => 'personal_website',
                'type' => 'url',
                'instructions' => 'Full URL to the author website.',
                'required' => 0,
            ],
            [
                'key' => 'field_movies_theme_author_twitter',
                'label' => 'Twitter',
                'name' => 'twitter',
                'type' => 'url',
                'instructions' => 'Full URL to the Twitter/X profile.',
                'required' => 0,
            ],
            [
                'key' => 'field_movies_theme_author_bluesky',
                'label' => 'Bluesky',
                'name' => 'bluesky',
                'type' => 'url',
                'instructions' => 'Full URL to the Bluesky profile.',
                'required' => 0,
            ],
            [
                'key' => 'field_movies_theme_author_letterboxd',
                'label' => 'Letterboxd',
                'name' => 'letterboxd',
                'type' => 'url',
                'instructions' => 'Full URL to the Letterboxd profile.',
                'required' => 0,
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'user_form',
                    'operator' => '==',
                    'value' => 'edit',
                ],
            ],
            [
                [
                    'param' => 'user_form',
                    'operator' => '==',
                    'value' => 'add',
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

      .postbox .handle-order-higher,
      .postbox .handle-order-lower {
        display: none !important;
      }

      #tagsdiv-post_tag {
        display: none !important;
      }

      .acf-relationship .choices li.movies-theme-choice-disabled {
        opacity: 0.35;
        pointer-events: none;
      }

      .movies-theme-native-field-edited::after {
        color: #b32d2e;
        content: '(edited)';
        font-size: 11px;
        font-style: italic;
        font-weight: 600;
        margin-left: 6px;
        text-transform: uppercase;
      }

      .movies-theme-edited-label {
        color: #b32d2e;
        font-size: 11px;
        font-style: italic;
        font-weight: 600;
        margin-left: 6px;
        text-transform: uppercase;
      }

      .movies-theme-previous-content {
        background: #f6f7f7;
        border: 1px solid #dcdcde;
        color: #646970;
        margin-top: 10px;
      }

      .movies-theme-previous-content summary {
        cursor: pointer;
        font-weight: 600;
        padding: 8px 10px;
      }

      .movies-theme-previous-content__value {
        border-top: 1px solid #dcdcde;
        padding: 10px;
        white-space: pre-wrap;
      }
    </style>
    <?php
});

add_action('acf/input/admin_footer', function (): void {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;

    if (!$screen) {
        return;
    }
    ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        <?php if ($screen->post_type === 'movies') : ?>
        var editedMovieFields = <?php echo wp_json_encode(
            current_user_can('edit_others_movies') ? movies_theme_get_edited_movie_fields() : []
        ); ?>;
        var previousMovieContent = <?php echo wp_json_encode(
            current_user_can('edit_others_movies') ? movies_theme_get_previous_movie_content() : []
        ); ?>;
        var nativeEditedFieldSelectors = {
          _post_title: '#titlewrap',
          _thumbnail_id: '#postimagediv .postbox-header h2',
          _category: '#categorydiv .postbox-header h2'
        };

        var markEditedAcfFields = function () {
          editedMovieFields.filter(function (fieldName) {
            return fieldName.charAt(0) !== '_';
          }).forEach(function (fieldName) {
            document.querySelectorAll('.acf-field[data-name="' + fieldName + '"] > .acf-label label').forEach(function (fieldLabel) {
              if (
                fieldLabel.querySelector('.movies-theme-edited-label')
                || fieldLabel.textContent.toLowerCase().includes('(edited)')
              ) {
                return;
              }

              var editedLabel = document.createElement('span');
              editedLabel.className = 'movies-theme-edited-label';
              editedLabel.textContent = '(edited)';
              fieldLabel.appendChild(editedLabel);
            });

            document.querySelectorAll('.acf-field[data-name="' + fieldName + '"] > .acf-input').forEach(function (fieldInput) {
              if (
                !Object.prototype.hasOwnProperty.call(previousMovieContent, fieldName)
                || fieldInput.querySelector('.movies-theme-previous-content')
              ) {
                return;
              }

              var previousContent = document.createElement('details');
              previousContent.className = 'movies-theme-previous-content';

              var previousContentLabel = document.createElement('summary');
              previousContentLabel.textContent = 'Previous content';

              var previousContentValue = document.createElement('div');
              previousContentValue.className = 'movies-theme-previous-content__value';
              previousContentValue.textContent = String(previousMovieContent[fieldName]);

              previousContent.appendChild(previousContentLabel);
              previousContent.appendChild(previousContentValue);
              fieldInput.appendChild(previousContent);
            });
          });
        };

        markEditedAcfFields();

        if (window.acf && typeof window.acf.addAction === 'function') {
          window.acf.addAction('append', markEditedAcfFields);
        }

        Object.keys(nativeEditedFieldSelectors).forEach(function (fieldName) {
          if (!editedMovieFields.includes(fieldName)) {
            return;
          }

          var fieldLabel = document.querySelector(nativeEditedFieldSelectors[fieldName]);

          if (fieldLabel) {
            fieldLabel.classList.add('movies-theme-native-field-edited');
          }
        });

        var pullquoteGroup = document.getElementById('acf-group_movies_theme_movie_pullquotes');
        var relatedGroup = document.getElementById('acf-group_movies_theme_movie_related');

        if (pullquoteGroup && !pullquoteGroup.classList.contains('closed')) {
          pullquoteGroup.classList.add('closed');
        }

        if (relatedGroup && !relatedGroup.classList.contains('closed')) {
          relatedGroup.classList.add('closed');
        }
        <?php endif; ?>

        <?php if ($screen->post_type === 'collection') : ?>
        var repeaterField = document.querySelector('.acf-field[data-name="collection_movie_entries"]');

        if (!repeaterField) {
          return;
        }

        var getRelationshipFields = function () {
          return Array.prototype.slice.call(
            repeaterField.querySelectorAll('.acf-field[data-name="movie"] .acf-relationship')
          );
        };

        var getSelectedIdsForField = function (relationshipField) {
          return Array.prototype.map.call(
            relationshipField.querySelectorAll('.values [data-id]'),
            function (item) {
              return String(item.getAttribute('data-id') || '');
            }
          ).filter(Boolean);
        };

        var refreshDisabledChoices = function () {
          var relationshipFields = getRelationshipFields();
          var allSelectedIds = relationshipFields.reduce(function (selectedIds, field) {
            return selectedIds.concat(getSelectedIdsForField(field));
          }, []);

          relationshipFields.forEach(function (field) {
            var ownSelectedIds = getSelectedIdsForField(field);

            field.querySelectorAll('.choices [data-id]').forEach(function (choice) {
              var choiceId = String(choice.getAttribute('data-id') || '');
              var isSelectedElsewhere = allSelectedIds.includes(choiceId) && !ownSelectedIds.includes(choiceId);

              choice.classList.toggle('movies-theme-choice-disabled', isSelectedElsewhere);
              choice.setAttribute('aria-disabled', isSelectedElsewhere ? 'true' : 'false');
            });
          });
        };

        repeaterField.addEventListener('click', function (event) {
          var choice = event.target.closest('.choices [data-id]');

          if (!choice || !choice.classList.contains('movies-theme-choice-disabled')) {
            return;
          }

          event.preventDefault();
          event.stopPropagation();
        }, true);

        var observer = new MutationObserver(refreshDisabledChoices);
        observer.observe(repeaterField, {
          childList: true,
          subtree: true,
          attributes: true,
          attributeFilter: ['class', 'data-id']
        });

        refreshDisabledChoices();
        <?php endif; ?>
      });
    </script>
    <?php
});

add_filter('acf/fields/wysiwyg/toolbars', function (array $toolbars): array {
    $toolbars['pullquote'] = [];
    $toolbars['pullquote'][1] = [
        'styleselect',
        'bold',
        'italic',
        'link',
        'unlink',
        'removeformat',
        'undo',
        'redo',
    ];

    return $toolbars;
});

add_filter('tiny_mce_before_init', function (array $init): array {
    $style_formats = [
        [
            'title' => 'Quote Normal',
            'inline' => 'span',
            'classes' => '',
        ],
        [
            'title' => 'Quote Bold',
            'inline' => 'span',
            'classes' => 'quote-bold',
        ],
        [
            'title' => 'Quote Huge',
            'inline' => 'span',
            'classes' => 'quote-huge',
        ],
        [
            'title' => 'Quote Highlight',
            'inline' => 'span',
            'classes' => 'quote-highlight',
        ],
    ];

    $init['style_formats'] = wp_json_encode($style_formats);

    return $init;
});

add_filter('acf/update_value/name=runtime', function ($value) {
    $value = trim((string) $value);

    if ($value === '') {
        return '';
    }

    if (preg_match('/\d+/', $value, $matches) !== 1) {
        return '';
    }

    return (string) ((int) $matches[0]);
});
