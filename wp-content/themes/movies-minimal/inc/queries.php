<?php

add_filter('query_vars', function (array $vars): array {
    $vars[] = 'movie_category';
    $vars[] = 'view';

    return $vars;
});

add_action('pre_get_posts', function (WP_Query $query): void {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->is_home()) {
        $query->set('post_type', 'collection');
        $query->set('posts_per_page', -1);

        if ((string) $query->get('view') === 'past-months') {
            $latest_collection = get_posts([
                'post_type' => 'collection',
                'posts_per_page' => 1,
                'fields' => 'ids',
                'no_found_rows' => true,
            ]);

            if ($latest_collection !== []) {
                $query->set('post__not_in', [(int) $latest_collection[0]]);
            }
        }
    }

    if ($query->is_author()) {
        $query->set('post_type', 'movies');
        $query->set('posts_per_page', -1);

        $category_slug = sanitize_title((string) $query->get('movie_category'));

        if ($category_slug !== '') {
            $query->set('tax_query', [
                [
                    'taxonomy' => 'category',
                    'field' => 'slug',
                    'terms' => $category_slug,
                ],
            ]);
        }
    }
});
