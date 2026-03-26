<?php

function movies_theme_get_scale_label_config(): array
{
    return [
        'funny' => [
            '0' => 'Not Applicable',
            '1' => 'Light Comedy',
            '2' => 'Pretty Funny',
            '3' => 'Hilarious',
        ],
        'scary' => [
            '0' => 'Not Applicable',
            '1' => 'Horror Light',
            '2' => 'Pretty Scary',
            '3' => 'Terrifying',
        ],
        'sadness' => [
            '0' => 'Not Applicable',
            '1' => 'Somewhat Morose',
            '2' => 'Pretty Sad',
            '3' => 'Devastating',
        ],
        'pacing' => [
            '0' => 'Not Applicable',
            '1' => 'Slow-Burn',
            '2' => 'Moderate',
            '3' => 'Fast',
        ],
    ];
}
