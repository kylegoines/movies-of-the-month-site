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
                'key' => 'field_movies_theme_movie_featured',
                'label' => 'Featured Movie',
                'name' => 'featured_movie',
                'type' => 'true_false',
                'instructions' => 'Feature this movie on the homepage.',
                'required' => 0,
                'ui' => 1,
                'default_value' => 0,
            ],
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
                'key' => 'field_movies_theme_movie_subtitle',
                'label' => 'Subtitle',
                'name' => 'subtitle',
                'type' => 'text',
                'instructions' => 'Secondary line shown with the movie title.',
                'required' => 0,
            ],
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
                'type' => 'text',
                'instructions' => 'Example: 121 min',
                'required' => 0,
            ],
            [
                'key' => 'field_movies_theme_movie_brief_synopsis',
                'label' => 'Brief Synopsis',
                'name' => 'brief_synopsis',
                'type' => 'textarea',
                'instructions' => 'Short synopsis shown in collections.',
                'required' => 0,
                'rows' => 4,
                'new_lines' => 'br',
            ],
            [
                'key' => 'field_movies_theme_movie_genre',
                'label' => 'Genre',
                'name' => 'genre',
                'type' => 'text',
                'instructions' => 'Short genre label used on the filter page.',
                'required' => 0,
            ],
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
