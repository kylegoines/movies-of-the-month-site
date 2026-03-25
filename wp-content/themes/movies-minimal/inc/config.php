<?php

function movies_theme_get_scale_label_config(): array
{
    return [
        'funny' => [
            '0' => '0 - Not Applicable',
            '1' => '1 - Light Comedy',
            '2' => '2 - Pretty Funny',
            '3' => '3 - Hilarious',
        ],
        'scary' => [
            '0' => '0 - Not Applicable',
            '1' => '1 - Horror Light',
            '2' => '2 - Pretty Scary',
            '3' => '3 - Terrifying',
        ],
        'sadness' => [
            '0' => '0 - Not Applicable',
            '1' => '1 - Somewhat Morose',
            '2' => '2 - Pretty Sad',
            '3' => '3 - Devastating',
        ],
        'pacing' => [
            '0' => '0 - Not Applicable',
            '1' => '1 - Slow-Burn',
            '2' => '2 - Moderate',
            '3' => '3 - Fast',
        ],
    ];
}
