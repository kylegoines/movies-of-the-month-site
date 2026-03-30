<?php

function movies_theme_get_scale_label_config(): array
{
    return [
        'funny' => [
            '0' => 'Not Funny',
            '1' => 'Light Comedy',
            '2' => 'Pretty Funny',
            '3' => 'Hilarious',
        ],
        'scary' => [
            '0' => 'Not Scary',
            '1' => 'Horror Light',
            '2' => 'Pretty Scary',
            '3' => 'Terrifying',
        ],
        'sadness' => [
            '0' => 'Not sad',
            '1' => 'Somewhat Morose',
            '2' => 'Pretty Sad',
            '3' => 'Devastating',
        ],
        'pacing' => [
            '0' => 'Slow-Burn',
            '1' => 'Moderate',
            '2' => 'Fast',
        ],
    ];
}
