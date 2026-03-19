<?php
/**
 * Plugin Name: Classic Editor for Local Theme Work
 * Description: Disables the block editor so the classic WordPress editor is used.
 */

add_filter('use_block_editor_for_post', '__return_false', 100);
add_filter('use_block_editor_for_post_type', '__return_false', 100);
