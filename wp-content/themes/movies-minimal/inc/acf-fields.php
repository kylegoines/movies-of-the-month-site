<?php

add_action('acf/init', function (): void {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    $scale_label_config = movies_theme_get_scale_label_config();

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
                'label' => 'Featured Movie',
                'name' => 'featured_movie',
                'type' => 'true_false',
                'instructions' => 'Feature movie mode (show feature content)',
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
        'title' => 'Featured Content',
        'fields' => [
            [
                'key' => 'field_movies_theme_movie_featured_image',
                'label' => 'Featured Image',
                'name' => 'featured_image',
                'type' => 'image',
                'instructions' => 'Optional image used for the homepage featured movie section.',
                'required' => 0,
                'return_format' => 'id',
                'preview_size' => 'medium',
                'library' => 'all',
            ],
            [
                'key' => 'field_movies_theme_movie_featured_content',
                'label' => 'Featured Content',
                'name' => 'featured_content',
                'type' => 'wysiwyg',
                'instructions' => 'Rich content shown when this movie is featured on the homepage.',
                'required' => 0,
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 0,
                'delay' => 0,
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
                'instructions' => 'Describe the film in a one or two sentence pitch. This will be shown on the collections view.',
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
        'key' => 'group_movies_theme_collection_movies',
        'title' => 'Collection Movies',
        'fields' => [
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
    </style>
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
