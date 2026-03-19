<?php
/**
 * Plugin Name: Activate Movies Minimal Theme
 * Description: Switches the local site to the bundled custom theme after installation.
 */

add_action('admin_init', function (): void {
    if (!function_exists('wp_get_theme')) {
        return;
    }

    $theme = wp_get_theme('movies-minimal');
    if (!$theme->exists()) {
        return;
    }

    if (get_option('stylesheet') === 'movies-minimal') {
        return;
    }

    switch_theme('movies-minimal');
});
