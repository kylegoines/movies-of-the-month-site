<?php

add_action('after_setup_theme', function (): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_post_type_support('post', 'excerpt');
});

add_action('init', function (): void {
    remove_role('author');
    remove_role('subscriber');

    $administrator = get_role('administrator');
    $contributor = get_role('contributor');
    $core_contributor = get_role('core_contributor');
    $editor = get_role('editor');

    $full_movie_and_collection_caps = [
        'edit_movies',
        'read_movie',
        'delete_movie',
        'edit_movie',
        'edit_others_movies',
        'publish_movies',
        'read_private_movies',
        'delete_movies',
        'delete_private_movies',
        'delete_published_movies',
        'delete_others_movies',
        'edit_private_movies',
        'edit_published_movies',
        'create_movies',
        'edit_collections',
        'read_collection',
        'delete_collection',
        'edit_collection',
        'edit_others_collections',
        'publish_collections',
        'read_private_collections',
        'delete_collections',
        'delete_private_collections',
        'delete_published_collections',
        'delete_others_collections',
        'edit_private_collections',
        'edit_published_collections',
        'create_collections',
        'manage_movie_badges',
        'edit_movie_badges',
        'delete_movie_badges',
        'assign_movie_badges',
    ];

    $contributor_movie_caps = [
        'upload_files',
        'edit_movies',
        'edit_published_movies',
        'delete_movies',
        'create_movies',
    ];

    $restricted_movie_and_collection_caps = [
        'publish_movies',
        'delete_published_movies',
        'edit_others_movies',
        'delete_others_movies',
        'read_private_movies',
        'edit_private_movies',
        'delete_private_movies',
        'create_collections',
        'edit_collections',
        'edit_published_collections',
        'delete_collections',
        'delete_published_collections',
        'publish_collections',
        'edit_others_collections',
        'delete_others_collections',
        'read_private_collections',
        'edit_private_collections',
        'delete_private_collections',
        'manage_movie_badges',
        'edit_movie_badges',
        'delete_movie_badges',
        'assign_movie_badges',
    ];

    $core_contributor_movie_caps = array_merge($contributor_movie_caps, [
        'publish_movies',
        'edit_published_movies',
        'delete_published_movies',
    ]);

    if ($administrator) {
        foreach ($full_movie_and_collection_caps as $capability) {
            $administrator->add_cap($capability);
        }
    }

    if ($administrator && $editor) {
        foreach ($administrator->capabilities as $capability => $granted) {
            if ($granted) {
                $editor->add_cap($capability);
            } else {
                $editor->remove_cap($capability);
            }
        }

        foreach ([
            'activate_plugins',
            'delete_plugins',
            'edit_plugins',
            'install_plugins',
            'update_plugins',
            'manage_options',
        ] as $capability) {
            $editor->remove_cap($capability);
        }
    }

    if (!$core_contributor && $contributor) {
        $core_contributor = add_role(
            'core_contributor',
            'Core Contributor',
            $contributor->capabilities
        );
    }

    if ($contributor) {
        foreach ($contributor_movie_caps as $capability) {
            $contributor->add_cap($capability);
        }

        foreach ($restricted_movie_and_collection_caps as $capability) {
            $contributor->remove_cap($capability);
        }
    }

    if (!$core_contributor) {
        return;
    }

    foreach ($core_contributor_movie_caps as $capability) {
        $core_contributor->add_cap($capability);
    }

    foreach ([
        'edit_others_movies',
        'delete_others_movies',
        'read_private_movies',
        'edit_private_movies',
        'delete_private_movies',
        'create_collections',
        'edit_collections',
        'edit_published_collections',
        'delete_collections',
        'delete_published_collections',
        'publish_collections',
        'edit_others_collections',
        'delete_others_collections',
        'read_private_collections',
        'edit_private_collections',
        'delete_private_collections',
        'manage_movie_badges',
        'edit_movie_badges',
        'delete_movie_badges',
        'assign_movie_badges',
    ] as $capability) {
        $core_contributor->remove_cap($capability);
    }
}, 20);

function movies_theme_sanitize_badge_svg(string $svg): string
{
    if (
        $svg === ''
        || !class_exists('DOMDocument')
        || stripos($svg, '<!DOCTYPE') !== false
        || stripos($svg, '<!ENTITY') !== false
    ) {
        return '';
    }

    $previous_error_state = libxml_use_internal_errors(true);
    $document = new DOMDocument();
    $loaded = $document->loadXML(
        $svg,
        LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_COMPACT
    );
    libxml_clear_errors();
    libxml_use_internal_errors($previous_error_state);

    $root = $document->documentElement;

    if (!$loaded || !($root instanceof DOMElement) || strtolower($root->localName) !== 'svg') {
        return '';
    }

    $allowed_elements = [
        'svg',
        'g',
        'path',
        'circle',
        'rect',
        'ellipse',
        'line',
        'polyline',
        'polygon',
        'title',
        'desc',
    ];
    $allowed_attributes = [
        'xmlns',
        'viewbox',
        'width',
        'height',
        'x',
        'y',
        'x1',
        'x2',
        'y1',
        'y2',
        'cx',
        'cy',
        'r',
        'rx',
        'ry',
        'd',
        'points',
        'fill',
        'fill-rule',
        'clip-rule',
        'stroke',
        'stroke-width',
        'stroke-linecap',
        'stroke-linejoin',
        'stroke-miterlimit',
        'opacity',
        'fill-opacity',
        'stroke-opacity',
        'transform',
        'preserveaspectratio',
        'vector-effect',
    ];
    $paint_attributes = [
        'fill',
        'stroke',
    ];

    $sanitize_element = static function (DOMElement $element) use (
        &$sanitize_element,
        $allowed_elements,
        $allowed_attributes,
        $paint_attributes
    ): void {
        $child_nodes = [];

        foreach ($element->childNodes as $child_node) {
            $child_nodes[] = $child_node;
        }

        foreach ($child_nodes as $child_node) {
            if (!($child_node instanceof DOMElement)) {
                continue;
            }

            if (
                !in_array(strtolower($child_node->localName), $allowed_elements, true)
                || (
                    $child_node->namespaceURI !== null
                    && $child_node->namespaceURI !== ''
                    && $child_node->namespaceURI !== 'http://www.w3.org/2000/svg'
                )
            ) {
                $element->removeChild($child_node);
                continue;
            }

            $sanitize_element($child_node);
        }

        $attributes = [];

        foreach ($element->attributes as $attribute) {
            $attributes[] = $attribute;
        }

        foreach ($attributes as $attribute) {
            $attribute_name = strtolower($attribute->name);

            if (
                str_starts_with($attribute_name, 'on')
                || !in_array($attribute_name, $allowed_attributes, true)
            ) {
                $element->removeAttributeNode($attribute);
                continue;
            }

            if (
                in_array($attribute_name, $paint_attributes, true)
                && preg_match(
                    '/^(?:none|currentcolor|transparent|#[0-9a-f]{3,8}|[a-z]+|rgba?\([0-9.,%\s]+\)|hsla?\([0-9.,%\s]+\))$/i',
                    trim($attribute->value)
                ) !== 1
            ) {
                $element->removeAttributeNode($attribute);
            }
        }
    };

    $sanitize_element($root);
    $sanitized_svg = $document->saveXML($root);

    return is_string($sanitized_svg) ? trim($sanitized_svg) : '';
}

add_filter('upload_mimes', function (array $mime_types): array {
    if (current_user_can('manage_movie_badges')) {
        $mime_types['svg'] = 'image/svg+xml';
    }

    return $mime_types;
});

add_filter('wp_check_filetype_and_ext', function (
    array $filetype,
    string $file,
    string $filename,
    ?array $mime_types,
    $real_mime = false
): array {
    unset($file, $mime_types, $real_mime);

    if (
        current_user_can('manage_movie_badges')
        && strtolower((string) pathinfo($filename, PATHINFO_EXTENSION)) === 'svg'
    ) {
        $filetype['ext'] = 'svg';
        $filetype['type'] = 'image/svg+xml';
        $filetype['proper_filename'] = false;
    }

    return $filetype;
}, 10, 5);

add_filter('wp_handle_upload_prefilter', function (array $file): array {
    $filename = isset($file['name']) ? (string) $file['name'] : '';

    if (strtolower((string) pathinfo($filename, PATHINFO_EXTENSION)) !== 'svg') {
        return $file;
    }

    if (!current_user_can('manage_movie_badges')) {
        $file['error'] = 'Only editors and administrators may upload badge SVG files.';
        return $file;
    }

    $file_size = isset($file['size']) ? (int) $file['size'] : 0;

    if ($file_size < 1 || $file_size > 512 * 1024) {
        $file['error'] = 'Badge SVG files must be smaller than 512 KB.';
        return $file;
    }

    $temporary_path = isset($file['tmp_name']) ? (string) $file['tmp_name'] : '';
    $svg = $temporary_path !== '' ? file_get_contents($temporary_path) : false;
    $sanitized_svg = is_string($svg) ? movies_theme_sanitize_badge_svg($svg) : '';

    if ($sanitized_svg === '') {
        $file['error'] = 'The badge SVG is invalid or contains unsupported markup.';
        return $file;
    }

    if (file_put_contents($temporary_path, $sanitized_svg) === false) {
        $file['error'] = 'The badge SVG could not be prepared for upload.';
        return $file;
    }

    $file['size'] = strlen($sanitized_svg);
    $file['type'] = 'image/svg+xml';

    return $file;
});

add_filter('wp_insert_post_data', function (array $data, array $postarr): array {
    if (($data['post_type'] ?? '') !== 'movies') {
        return $data;
    }

    $post_id = isset($postarr['ID']) ? (int) $postarr['ID'] : 0;

    $current_status = $post_id > 0 ? get_post_status($post_id) : false;

    if ($post_id < 1 || !in_array($current_status, ['publish', 'pending'], true)) {
        return $data;
    }

    $user = wp_get_current_user();

    if (
        !($user instanceof WP_User)
        || !in_array('contributor', $user->roles, true)
        || current_user_can('publish_movies')
    ) {
        return $data;
    }

    $GLOBALS['movies_theme_movie_edit_snapshot'] = [
        'post_id' => $post_id,
        'title' => get_the_title($post_id),
        'thumbnail_id' => get_post_thumbnail_id($post_id),
        'categories' => wp_get_object_terms($post_id, 'category', ['fields' => 'ids']),
        'fields' => function_exists('get_fields') ? (get_fields($post_id) ?: []) : [],
    ];

    if (in_array($data['post_status'] ?? '', ['publish', 'future'], true)) {
        $data['post_status'] = 'pending';
    }

    return $data;
}, 10, 2);

function movies_theme_format_previous_field_value(int $post_id, string $field_name, $value): ?string
{
    $field = function_exists('get_field_object')
        ? get_field_object($field_name, $post_id, false, false)
        : null;
    $field_type = is_array($field) ? (string) ($field['type'] ?? '') : '';

    if (in_array($field_type, ['image', 'file', 'gallery'], true)) {
        return null;
    }

    if ($field_type === 'true_false') {
        return $value ? 'Checked' : 'Unchecked';
    }

    $scale_label_config = movies_theme_get_scale_label_config();

    if (
        isset($scale_label_config[$field_name])
        && array_key_exists((string) $value, $scale_label_config[$field_name])
    ) {
        return (string) $scale_label_config[$field_name][(string) $value];
    }

    if (
        in_array($field_type, ['select', 'radio', 'checkbox', 'button_group'], true)
        && is_array($field)
        && !empty($field['choices'])
        && is_array($field['choices'])
    ) {
        $selected_values = is_array($value) ? $value : [$value];
        $selected_labels = [];

        foreach ($selected_values as $selected_value) {
            $choice_key = (string) $selected_value;
            $selected_labels[] = array_key_exists($choice_key, $field['choices'])
                ? (string) $field['choices'][$choice_key]
                : $choice_key;
        }

        $selected_labels = array_values(array_filter(
            $selected_labels,
            static fn(string $label): bool => $label !== ''
        ));

        return $selected_labels === [] ? '(empty)' : implode("\n", $selected_labels);
    }

    $normalize_value = static function ($item) use (&$normalize_value): array {
        if ($item instanceof WP_Post) {
            return [get_the_title($item)];
        }

        if ($item instanceof WP_Term) {
            return [$item->name];
        }

        if (is_array($item)) {
            $normalized = [];

            foreach ($item as $child) {
                $normalized = array_merge($normalized, $normalize_value($child));
            }

            return $normalized;
        }

        if (is_bool($item)) {
            return [$item ? 'Checked' : 'Unchecked'];
        }

        if ($item === null || $item === '') {
            return [];
        }

        return [trim(wp_strip_all_tags((string) $item))];
    };

    $normalized_values = array_values(array_filter(
        $normalize_value($value),
        static fn(string $item): bool => $item !== ''
    ));

    return $normalized_values === [] ? '(empty)' : implode("\n", $normalized_values);
}

add_action('acf/save_post', function ($post_id): void {
    $post_id = (int) $post_id;
    $snapshot = $GLOBALS['movies_theme_movie_edit_snapshot'] ?? null;

    if (!is_array($snapshot) || ($snapshot['post_id'] ?? 0) !== $post_id) {
        return;
    }

    unset($GLOBALS['movies_theme_movie_edit_snapshot']);

    $edited_fields = get_post_meta($post_id, '_movies_theme_edited_fields', true);
    $edited_fields = is_array($edited_fields) ? $edited_fields : [];
    $previous_content = get_post_meta($post_id, '_movies_theme_previous_content', true);
    $previous_content = is_array($previous_content) ? $previous_content : [];
    $current_fields = function_exists('get_fields') ? (get_fields($post_id) ?: []) : [];

    foreach (array_unique(array_merge(array_keys($snapshot['fields']), array_keys($current_fields))) as $field_name) {
        $previous_value = $snapshot['fields'][$field_name] ?? null;
        $current_value = $current_fields[$field_name] ?? null;

        if (maybe_serialize($previous_value) !== maybe_serialize($current_value)) {
            $edited_fields[] = (string) $field_name;

            if (!array_key_exists($field_name, $previous_content)) {
                $formatted_previous_value = movies_theme_format_previous_field_value(
                    $post_id,
                    (string) $field_name,
                    $previous_value
                );

                if ($formatted_previous_value !== null) {
                    $previous_content[$field_name] = $formatted_previous_value;
                }
            }
        }
    }

    if ((string) $snapshot['title'] !== get_the_title($post_id)) {
        $edited_fields[] = '_post_title';
    }

    if ((int) $snapshot['thumbnail_id'] !== get_post_thumbnail_id($post_id)) {
        $edited_fields[] = '_thumbnail_id';
    }

    $previous_categories = array_map('intval', is_array($snapshot['categories']) ? $snapshot['categories'] : []);
    $current_category_ids = wp_get_object_terms($post_id, 'category', ['fields' => 'ids']);
    $current_categories = array_map('intval', is_array($current_category_ids) ? $current_category_ids : []);
    sort($previous_categories);
    sort($current_categories);

    if ($previous_categories !== $current_categories) {
        $edited_fields[] = '_category';
    }

    $edited_fields = array_values(array_unique($edited_fields));

    if ($edited_fields === []) {
        delete_post_meta($post_id, '_movies_theme_edited_fields');
        delete_post_meta($post_id, '_movies_theme_previous_content');

        return;
    }

    update_post_meta($post_id, '_movies_theme_edited_fields', $edited_fields);
    update_post_meta($post_id, '_movies_theme_previous_content', $previous_content);
}, 20);

add_action('transition_post_status', function (string $new_status, string $old_status, WP_Post $post): void {
    if (
        $post->post_type === 'movies'
        && $new_status === 'publish'
        && $old_status !== 'publish'
        && current_user_can('publish_movies')
    ) {
        delete_post_meta((int) $post->ID, '_movies_theme_edited_fields');
        delete_post_meta((int) $post->ID, '_movies_theme_previous_content');
    }
}, 10, 3);

add_action('init', function (): void {
    global $wp_rewrite;
    $wp_rewrite->author_base = 'contributors';
});

add_action('pre_get_posts', function (WP_Query $query): void {
    if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'movies') {
        return;
    }

    $user = wp_get_current_user();

    if (!($user instanceof WP_User) || $user->roles !== ['contributor']) {
        return;
    }

    // WordPress limits this list to the current author when edit_others_movies
    // is unavailable. Contributors may browse the shared list without gaining
    // permission to edit or delete another contributor's movies.
    $query->set('author', '');
}, 100);

add_action('admin_menu', function (): void {
    $user = wp_get_current_user();

    if (!($user instanceof WP_User)) {
        return;
    }

    if ($user->roles === ['contributor']) {
        remove_menu_page('edit.php');
        remove_menu_page('edit-comments.php');
        remove_menu_page('edit.php?post_type=page');
        remove_menu_page('plugins.php');
        remove_menu_page('themes.php');
        remove_menu_page('tools.php');
        remove_menu_page('options-general.php');
        remove_menu_page('edit.php?post_type=acf-field-group');
        remove_menu_page('wp-mail-smtp');

        return;
    }

    if ($user->roles === ['editor']) {
        remove_menu_page('edit.php');
        remove_menu_page('edit-comments.php');
        remove_menu_page('plugins.php');
        remove_menu_page('themes.php');
        remove_menu_page('tools.php');
        remove_menu_page('options-general.php');
        remove_menu_page('edit.php?post_type=acf-field-group');
        remove_menu_page('wp-mail-smtp');
    }
}, 999);

add_action('admin_menu', function (): void {
    if (!current_user_can('manage_categories')) {
        return;
    }

    add_menu_page(
        'Categories',
        'Categories',
        'manage_categories',
        'edit-tags.php?taxonomy=category',
        '',
        'dashicons-tag',
        24
    );
}, 20);

add_action('wp_dashboard_setup', function (): void {
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');

    wp_add_dashboard_widget(
        'movies_theme_recent_movie_activity',
        'Recent Movie Activity',
        'movies_theme_render_recent_movie_activity_widget'
    );
});

add_action('admin_notices', function (): void {
    $screen = get_current_screen();

    if (!($screen instanceof WP_Screen) || $screen->base !== 'dashboard') {
        return;
    }

    if (isset($_COOKIE['movies_theme_hide_contributor_guide']) && $_COOKIE['movies_theme_hide_contributor_guide'] === '1') {
        return;
    }

    movies_theme_render_contributor_guide_panel();
});

add_action('admin_head-index.php', function (): void {
    ?>
    <style>
      body.index-php .notice.notice-warning {
        display: none !important;
      }
    </style>
    <?php
});

add_action('admin_menu', function (): void {
    add_submenu_page(
        'edit.php?post_type=mailing_signup',
        'Export Mailing List',
        'Export Emails',
        'edit_posts',
        'movies-theme-mailing-export',
        'movies_theme_render_mailing_export_page'
    );
});

add_action('admin_notices', function (): void {
    $screen = get_current_screen();

    if (!($screen instanceof WP_Screen) || $screen->base !== 'edit' || $screen->post_type !== 'mailing_signup') {
        return;
    }

    $export_url = admin_url('edit.php?post_type=mailing_signup&page=movies-theme-mailing-export');
    ?>
    <div class="notice notice-info">
      <p>
        <a class="button button-primary" href="<?php echo esc_url($export_url); ?>">Get Comma-Separated Email List</a>
      </p>
    </div>
    <?php
});

function movies_theme_render_contributor_guide_panel(): void
{
    ?>
    <style>
      .movies-theme-dashboard-guide-wrap {
        margin: 16px 0 20px;
      }

      .movies-theme-dashboard-guide-wrap .notice-dismiss {
        display: none;
      }

      .movies-theme-dashboard-guide {
        background: #ffffff;
        border: 1px solid #dcdcde;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
        color: #111111;
        font-size: 30px !important;
        line-height: 1.38 !important;
        padding: 32px 36px 34px;
        position: relative;
      }

      .movies-theme-dashboard-guide__title {
        font-size: 48px !important;
        font-weight: 700;
        letter-spacing: -0.03em;
        line-height: 1.08;
        margin: 0 0 38px;
        padding-top: 28px;
      }

      .movies-theme-dashboard-guide__accent {
        color: #d946ef;
      }

      .movies-theme-dashboard-guide p {
        font-size: 30px !important;
        line-height: 1.38 !important;
        margin: 0 0 30px;
        max-width: 920px;
      }

      .movies-theme-dashboard-guide p:last-child {
        margin-bottom: 0;
      }

      .movies-theme-dashboard-guide__close {
        appearance: none;
        position: absolute;
        right: 36px;
        top: 24px;
        background: transparent;
        border: 0;
        color: #6b7280;
        cursor: pointer;
        font-size: 15px;
        font-weight: 600;
        letter-spacing: 0.01em;
        padding: 0;
        text-transform: uppercase;
      }

      .movies-theme-dashboard-guide__close:hover,
      .movies-theme-dashboard-guide__close:focus {
        color: #111111;
      }
    </style>
    <div class="notice notice-info is-dismissible movies-theme-dashboard-guide-wrap" data-guide-cookie="movies_theme_hide_contributor_guide">
      <div class="movies-theme-dashboard-guide">
        <button type="button" class="movies-theme-dashboard-guide__close">Close this message</button>
        <h2 class="movies-theme-dashboard-guide__title">
          Movies of the Month <span class="movies-theme-dashboard-guide__accent">Contributor Guide</span>
        </h2>
        <p>We are excited to have you join us as a contributor at Movies of the Month!</p>
        <p>Each month Nasha will post the “Movies of the Month” - a collection of recommendations from our contributors.</p>
        <p>As a contributor, you can add recommendations and editorials (articles) on a rolling basis. Nasha will curate the list each month.</p>
        <p>Before the “Movies of the Month” goes live, contributors will have an option to give their own feedback on movies being recommended by other contributors. So if you love (or dislike!) a movie, you can back a recommendation or (politely) give your own two cents.</p>
      </div>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var guide = document.querySelector('.movies-theme-dashboard-guide-wrap');

        if (!guide) {
          return;
        }

        var cookieName = guide.getAttribute('data-guide-cookie');

        if (!cookieName) {
          return;
        }

        var dismissButton = guide.querySelector('.movies-theme-dashboard-guide__close');

        if (!dismissButton) {
          return;
        }

        dismissButton.addEventListener('click', function () {
          var expires = new Date();
          expires.setFullYear(expires.getFullYear() + 1);
          document.cookie = cookieName + '=1; expires=' + expires.toUTCString() + '; path=/; SameSite=Lax';
          guide.classList.add('is-hidden');
        });
      });
    </script>
    <?php
}

function movies_theme_render_recent_movie_activity_widget(): void
{
    $recent_movies = get_posts([
        'post_type' => 'movies',
        'post_status' => ['publish', 'draft', 'pending', 'future', 'private'],
        'posts_per_page' => 20,
        'orderby' => 'date',
        'order' => 'DESC',
        'no_found_rows' => true,
    ]);

    $poster_items = [];

    foreach ($recent_movies as $movie_post) {
        if (!($movie_post instanceof WP_Post)) {
            continue;
        }

        $poster_html = get_the_post_thumbnail((int) $movie_post->ID, 'medium', [
            'loading' => 'lazy',
            'decoding' => 'async',
            'alt' => get_the_title($movie_post),
        ]);

        if ($poster_html === '') {
            continue;
        }

        $poster_items[] = [
            'title' => get_the_title($movie_post),
            'edit_link' => get_edit_post_link((int) $movie_post->ID),
            'poster_html' => $poster_html,
        ];
    }

    ?>
    <style>
      #movies_theme_recent_movie_activity .inside {
        margin: 0;
        padding: 18px 20px 20px;
      }

      .movies-theme-dashboard-marquee {
        overflow: hidden;
        position: relative;
      }

      .movies-theme-dashboard-marquee::after,
      .movies-theme-dashboard-marquee::before {
        content: '';
        pointer-events: none;
        position: absolute;
        top: 0;
        bottom: 0;
        width: 44px;
        z-index: 2;
      }

      .movies-theme-dashboard-marquee::before {
        background: linear-gradient(90deg, #ffffff 0%, rgba(255, 255, 255, 0) 100%);
        left: 0;
      }

      .movies-theme-dashboard-marquee::after {
        background: linear-gradient(270deg, #ffffff 0%, rgba(255, 255, 255, 0) 100%);
        right: 0;
      }

      .movies-theme-dashboard-marquee__track {
        align-items: stretch;
        display: flex;
        gap: 16px;
        width: max-content;
        animation: movies-theme-dashboard-marquee-scroll 36s linear infinite;
      }

      .movies-theme-dashboard-marquee:hover .movies-theme-dashboard-marquee__track {
        animation-play-state: paused;
      }

      .movies-theme-dashboard-marquee__item {
        display: block;
        flex: 0 0 auto;
        text-decoration: none;
        width: 132px;
      }

      .movies-theme-dashboard-marquee__item:focus {
        box-shadow: none;
        outline: none;
      }

      .movies-theme-dashboard-marquee__poster {
        aspect-ratio: 2 / 3;
        background: #f6f7f7;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(17, 17, 17, 0.08);
        overflow: hidden;
      }

      .movies-theme-dashboard-marquee__poster img {
        display: block;
        height: 100%;
        object-fit: cover;
        width: 100%;
      }

      .movies-theme-dashboard-marquee__caption {
        color: #1d2327;
        display: block;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.35;
        margin-top: 10px;
      }

      @keyframes movies-theme-dashboard-marquee-scroll {
        from {
          transform: translateX(0);
        }

        to {
          transform: translateX(calc(-50% - 8px));
        }
      }
    </style>
    <?php if ($poster_items === []) : ?>
      <p>No movie posters available yet.</p>
    <?php else : ?>
      <?php $loop_items = array_merge($poster_items, $poster_items); ?>
      <div class="movies-theme-dashboard-marquee" aria-label="Recent movie posters">
        <div class="movies-theme-dashboard-marquee__track">
          <?php foreach ($loop_items as $index => $item) : ?>
            <?php
            $title = is_string($item['title']) ? $item['title'] : '';
            $edit_link = is_string($item['edit_link']) ? $item['edit_link'] : '';
            $poster_html = is_string($item['poster_html']) ? $item['poster_html'] : '';
            $is_duplicate = $index >= count($poster_items);
            ?>
            <a
              class="movies-theme-dashboard-marquee__item"
              href="<?php echo esc_url($edit_link !== '' ? $edit_link : '#'); ?>"
              <?php echo $is_duplicate ? 'aria-hidden="true" tabindex="-1"' : ''; ?>
            >
              <span class="movies-theme-dashboard-marquee__poster"><?php echo $poster_html; ?></span>
              <span class="movies-theme-dashboard-marquee__caption"><?php echo esc_html($title); ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
    <?php
}

function movies_theme_get_mailing_signup_emails(): array
{
    $signup_ids = get_posts([
        'post_type' => 'mailing_signup',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'orderby' => 'date',
        'order' => 'DESC',
    ]);

    $emails = [];

    foreach ($signup_ids as $signup_id) {
        $email = get_post_meta((int) $signup_id, '_movies_theme_mailing_signup_email', true);

        if (!is_string($email) || $email === '') {
            continue;
        }

        $emails[] = $email;
    }

    return $emails;
}

function movies_theme_render_mailing_export_page(): void
{
    if (!current_user_can('edit_posts')) {
        wp_die('You do not have permission to view this page.');
    }

    $emails = movies_theme_get_mailing_signup_emails();
    $email_string = implode(', ', $emails);
    ?>
    <div class="wrap">
      <h1>Export Mailing List</h1>
      <p>Copy the full mailing list as one comma-separated string.</p>

      <p>
        <a class="button" href="<?php echo esc_url(admin_url('edit.php?post_type=mailing_signup')); ?>">Back to Mailing List</a>
      </p>

      <textarea
        id="movies-theme-mailing-export"
        class="large-text code"
        rows="12"
        readonly
      ><?php echo esc_textarea($email_string); ?></textarea>

      <p>
        <button type="button" class="button button-primary" id="movies-theme-copy-mailing-export">Copy Emails</button>
      </p>

      <p><strong><?php echo esc_html((string) count($emails)); ?></strong> email<?php echo count($emails) === 1 ? '' : 's'; ?> in list.</p>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var button = document.getElementById('movies-theme-copy-mailing-export');
        var field = document.getElementById('movies-theme-mailing-export');

        if (!button || !field) {
          return;
        }

        button.addEventListener('click', function () {
          if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(field.value).then(function () {
              button.textContent = 'Copied';
            }).catch(function () {
              field.focus();
              field.select();
              button.textContent = 'Press Cmd/Ctrl+C';
            });

            return;
          }

          field.focus();
          field.select();
          button.textContent = 'Press Cmd/Ctrl+C';
        });
      });
    </script>
    <?php
}

function movies_theme_get_mailing_post_subject(int $post_id): string
{
    $subject = get_post_meta($post_id, 'mailing_post_subject', true);

    if (is_string($subject) && trim($subject) !== '') {
        return trim($subject);
    }

    return get_the_title($post_id);
}

function movies_theme_get_mailing_post_body(int $post_id): string
{
    $post = get_post($post_id);

    if (!($post instanceof WP_Post)) {
        return '';
    }

    $body = trim((string) $post->post_content);

    if ($body === '') {
        return '';
    }

    return wpautop(wp_kses_post($body));
}

add_action('add_meta_boxes', function (): void {
    add_meta_box(
        'movies_theme_mailing_post_send',
        'Send Email Campaign',
        'movies_theme_render_mailing_post_send_meta_box',
        'mailing_post',
        'side',
        'high'
    );
});

function movies_theme_render_mailing_post_send_meta_box(WP_Post $post): void
{
    $recipient_count = count(movies_theme_get_mailing_signup_emails());
    $sent_at = (int) get_post_meta((int) $post->ID, '_movies_theme_mailing_post_sent_at', true);
    $sent_count = (int) get_post_meta((int) $post->ID, '_movies_theme_mailing_post_sent_count', true);
    $send_url = wp_nonce_url(
        admin_url('admin-post.php?action=movies_theme_send_mailing_post&post_id=' . (int) $post->ID),
        'movies_theme_send_mailing_post_' . (int) $post->ID
    );
    ?>
    <p>This sends the saved version of this email to everyone currently on the mailing list.</p>
    <p><strong>Current recipients:</strong> <?php echo esc_html((string) $recipient_count); ?></p>
    <p><strong>Email subject:</strong> <?php echo esc_html(movies_theme_get_mailing_post_subject((int) $post->ID)); ?></p>
    <?php if ($sent_at > 0) : ?>
      <p><strong>Last sent:</strong> <?php echo esc_html(wp_date('F j, Y g:i a', $sent_at)); ?></p>
      <p><strong>Last recipient count:</strong> <?php echo esc_html((string) $sent_count); ?></p>
    <?php endif; ?>
    <p>
      <a class="button button-primary" href="<?php echo esc_url($send_url); ?>">
        <?php echo $sent_at > 0 ? 'Send Again' : 'Send Email'; ?>
      </a>
    </p>
    <p><em>Save updates before sending.</em></p>
    <?php
}

add_action('admin_notices', function (): void {
    $screen = get_current_screen();

    if (!($screen instanceof WP_Screen)) {
        return;
    }

    if ($screen->post_type !== 'mailing_post') {
        return;
    }

    $status = isset($_GET['mailing_post_status'])
        ? sanitize_key(wp_unslash($_GET['mailing_post_status']))
        : '';

    if ($status === '') {
        return;
    }

    $messages = [
        'sent' => ['success', 'Email sent to the mailing list.'],
        'empty' => ['error', 'Add an email subject and body before sending.'],
        'no_recipients' => ['warning', 'No mailing list recipients were found.'],
        'permission' => ['error', 'You do not have permission to send this email.'],
    ];

    if (!isset($messages[$status])) {
        return;
    }

    [$type, $message] = $messages[$status];
    ?>
    <div class="notice notice-<?php echo esc_attr($type); ?> is-dismissible">
      <p><?php echo esc_html($message); ?></p>
    </div>
    <?php
});

add_filter('custom_menu_order', '__return_true');

add_filter('editable_roles', function (array $roles): array {
    $preferred_order = [
        'administrator',
        'editor',
        'core_contributor',
        'contributor',
    ];

    $ordered_roles = [];

    foreach ($preferred_order as $role_key) {
        if (isset($roles[$role_key])) {
            $ordered_roles[$role_key] = $roles[$role_key];
        }
    }

    foreach ($roles as $role_key => $role_config) {
        if (!isset($ordered_roles[$role_key])) {
            $ordered_roles[$role_key] = $role_config;
        }
    }

    return $ordered_roles;
});

add_action('category_add_form_fields', function (): void {
    ?>
    <div class="form-field term-group">
        <label for="movies-theme-category-color">Category Color</label>
        <input type="color" id="movies-theme-category-color" name="movies_theme_color" value="#111111">
        <p>Optional color used for category chips on the site.</p>
    </div>
    <div class="form-field term-group">
        <label for="movies-theme-invert-chip-text">
            <input type="checkbox" id="movies-theme-invert-chip-text" name="movies_theme_invert_chip_text" value="1">
            Invert chip text color
        </label>
        <p>Flip the chip text between black and white instead of using the automatic contrast choice.</p>
    </div>
    <?php
});

add_action('category_edit_form_fields', function (WP_Term $term): void {
    $color = movies_theme_get_category_color((int) $term->term_id);
    $invert_chip_text = movies_theme_should_invert_category_chip_text((int) $term->term_id);
    ?>
    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="movies-theme-category-color">Category Color</label></th>
        <td>
            <input type="color" id="movies-theme-category-color" name="movies_theme_color" value="<?php echo esc_attr($color !== '' ? $color : '#111111'); ?>">
            <p class="description">Optional color used for category chips on the site.</p>
        </td>
    </tr>
    <tr class="form-field term-group-wrap">
        <th scope="row">Invert Chip Text</th>
        <td>
            <label for="movies-theme-invert-chip-text">
                <input type="checkbox" id="movies-theme-invert-chip-text" name="movies_theme_invert_chip_text" value="1" <?php checked($invert_chip_text); ?>>
                Flip the chip text between black and white instead of using the automatic contrast choice.
            </label>
        </td>
    </tr>
    <?php
});

$movies_theme_save_category_color = function (int $term_id): void {
    if (!current_user_can('manage_categories')) {
        return;
    }

    $raw_color = isset($_POST['movies_theme_color']) ? wp_unslash($_POST['movies_theme_color']) : '';
    $color = sanitize_hex_color((string) $raw_color);

    if (is_string($color) && $color !== '') {
        update_term_meta($term_id, 'movies_theme_color', $color);
    } else {
        delete_term_meta($term_id, 'movies_theme_color');
    }

    update_term_meta(
        $term_id,
        'movies_theme_invert_chip_text',
        isset($_POST['movies_theme_invert_chip_text']) ? '1' : '0'
    );
};

add_action('created_category', $movies_theme_save_category_color);
add_action('edited_category', $movies_theme_save_category_color);

add_filter('menu_order', function (array $menu_order): array {
    $user = wp_get_current_user();

    if (!($user instanceof WP_User)) {
        return $menu_order;
    }

    if ($user->roles === ['editor']) {
        $preferred_order = [
            'index.php',
            'edit.php?post_type=movies',
            'edit.php?post_type=home_intro',
            'edit.php?post_type=collection',
            'edit.php?post_type=mailing_signup',
            'edit.php?post_type=mailing_post',
            'edit-tags.php?taxonomy=category',
            'users.php',
            'upload.php',
            'edit.php?post_type=page',
        ];

        $reordered = [];

        foreach ($preferred_order as $menu_slug) {
            if (in_array($menu_slug, $menu_order, true)) {
                $reordered[] = $menu_slug;
            }
        }

        foreach ($menu_order as $menu_slug) {
            if (!in_array($menu_slug, $reordered, true)) {
                $reordered[] = $menu_slug;
            }
        }

        return $reordered;
    }

    if ($user->roles !== ['contributor']) {
        return $menu_order;
    }

    $preferred_order = [
        'edit.php?post_type=movies',
        'edit.php?post_type=collection',
        'upload.php',
    ];

    $ordered = [];

    foreach ($preferred_order as $menu_slug) {
        if (in_array($menu_slug, $menu_order, true)) {
            $ordered[] = $menu_slug;
        }
    }

    foreach ($menu_order as $menu_slug) {
        if (!in_array($menu_slug, $ordered, true)) {
            $ordered[] = $menu_slug;
        }
    }

    return $ordered;
});

add_action('admin_head-profile.php', function (): void {
    ?>
    <style>
      .user-syntax-highlighting-wrap,
      .user-comment-shortcuts-wrap,
      .user-display-name-wrap {
        display: none !important;
      }
    </style>
    <?php
});

add_action('admin_head-user-edit.php', function (): void {
    ?>
    <style>
      .user-syntax-highlighting-wrap,
      .user-comment-shortcuts-wrap,
      .user-display-name-wrap {
        display: none !important;
      }
    </style>
    <?php
});

add_action('admin_footer-profile.php', 'movies_theme_render_nickname_note');
add_action('admin_footer-user-edit.php', 'movies_theme_render_nickname_note');

function movies_theme_render_nickname_note(): void
{
    ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const nicknameRow = document.querySelector('.user-nickname-wrap');

        if (!nicknameRow || nicknameRow.querySelector('.movies-theme-nickname-note')) {
          return;
        }

        const description = document.createElement('p');
        description.className = 'description movies-theme-nickname-note';
        description.textContent = 'This is the user-facing name shown with your posts, editorials, and recommendations.';

        const nicknameCell = nicknameRow.querySelector('td');

        if (nicknameCell) {
          nicknameCell.appendChild(description);
        }
      });
    </script>
    <?php
}

add_action('wp_enqueue_scripts', function (): void {
    $theme = wp_get_theme();
    $theme_version = $theme->get('Version');
    $theme_dir = get_template_directory();
    $theme_uri = get_template_directory_uri();
    $manifest_path = $theme_dir . '/dist/.vite/manifest.json';

    if (!file_exists($manifest_path)) {
        wp_enqueue_style(
            'movies-theme-style',
            get_stylesheet_uri(),
            [],
            $theme_version
        );

        return;
    }

    $manifest = json_decode(file_get_contents($manifest_path), true);
    $entry = $manifest['src/main.js'] ?? null;

    if (!$entry) {
        return;
    }

    if (!empty($entry['css'])) {
        foreach ($entry['css'] as $index => $css_file) {
            wp_enqueue_style(
                'movie-app-' . $index,
                $theme_uri . '/dist/' . $css_file,
                [],
                $theme_version
            );
        }
    }

    if (!empty($entry['file'])) {
        wp_enqueue_script(
            'movie-app',
            $theme_uri . '/dist/' . $entry['file'],
            [],
            $theme_version,
            true
        );
        wp_script_add_data('movie-app', 'type', 'module');
        wp_localize_script('movie-app', 'movieApp', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'heartNonce' => wp_create_nonce('movies_theme_toggle_heart'),
        ]);
    }
});

add_action('wp_head', function (): void {
    $favicon_url = get_template_directory_uri() . '/images/favicon-sparkle.svg';
    ?>
    <link rel="icon" href="<?php echo esc_url($favicon_url); ?>" type="image/svg+xml">
    <link rel="shortcut icon" href="<?php echo esc_url($favicon_url); ?>" type="image/svg+xml">
    <?php
}, 1);

add_action('wp_head', function (): void {
    if (!is_singular('movies')) {
        return;
    }

    $post_id = get_queried_object_id();

    if ($post_id <= 0) {
        return;
    }

    $title = wp_get_document_title();
    $description = movies_theme_get_the_pitch($post_id);

    if ($description === '') {
        $description = trim((string) get_the_excerpt($post_id));
    }

    if ($description === '') {
        $description = trim(wp_strip_all_tags((string) get_post_field('post_content', $post_id)));
    }

    if ($description !== '') {
        $description = wp_trim_words($description, 30, '...');
    }

    $permalink = get_permalink($post_id);
    $poster_url = get_the_post_thumbnail_url($post_id, 'full');

    if (!is_string($poster_url) || $poster_url === '') {
        return;
    }

    $poster_id = get_post_thumbnail_id($post_id);
    $poster_width = 0;
    $poster_height = 0;

    if ($poster_id > 0) {
        $poster_meta = wp_get_attachment_metadata($poster_id);

        if (is_array($poster_meta)) {
            $poster_width = (int) ($poster_meta['width'] ?? 0);
            $poster_height = (int) ($poster_meta['height'] ?? 0);
        }
    }
    ?>
    <meta property="og:type" content="article">
    <meta property="og:title" content="<?php echo esc_attr($title); ?>">
    <meta property="og:url" content="<?php echo esc_url($permalink); ?>">
    <?php if ($description !== '') : ?>
      <meta property="og:description" content="<?php echo esc_attr($description); ?>">
      <meta name="twitter:description" content="<?php echo esc_attr($description); ?>">
    <?php endif; ?>
    <meta property="og:image" content="<?php echo esc_url($poster_url); ?>">
    <meta name="twitter:title" content="<?php echo esc_attr($title); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="<?php echo esc_url($poster_url); ?>">
    <?php if ($poster_width > 0) : ?>
      <meta property="og:image:width" content="<?php echo esc_attr((string) $poster_width); ?>">
    <?php endif; ?>
    <?php if ($poster_height > 0) : ?>
      <meta property="og:image:height" content="<?php echo esc_attr((string) $poster_height); ?>">
    <?php endif; ?>
    <?php
}, 5);

add_action('save_post_movies', function (int $post_id, WP_Post $post, bool $update): void {
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return;
    }

    if ($post->post_status !== 'publish') {
        return;
    }

    if (!function_exists('get_field')) {
        return;
    }

    $featured_content = trim((string) get_field('featured_content', $post_id));

    if ($featured_content === '') {
        return;
    }

    $logged_at = (int) get_post_meta($post_id, '_movies_theme_editorial_logged_at', true);

    if ($logged_at > 0) {
        return;
    }

    $editorial_author_id = movies_theme_get_editorial_author_id($post_id);

    if ($editorial_author_id <= 0) {
        $editorial_author_id = (int) $post->post_author;
    }

    update_post_meta($post_id, '_movies_theme_editorial_logged_at', time());
    update_post_meta($post_id, '_movies_theme_editorial_logged_author', $editorial_author_id);
}, 10, 3);

add_action('init', function (): void {
    if (get_option('movies_theme_editorial_activity_backfilled', false)) {
        return;
    }

    if (!function_exists('get_field')) {
        return;
    }

    $movies = get_posts([
        'post_type' => 'movies',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'ASC',
        'no_found_rows' => true,
    ]);

    foreach ($movies as $movie) {
        if (!$movie instanceof WP_Post) {
            continue;
        }

        $post_id = (int) $movie->ID;
        $logged_at = (int) get_post_meta($post_id, '_movies_theme_editorial_logged_at', true);

        if ($logged_at > 0) {
            continue;
        }

        $featured_content = trim((string) get_field('featured_content', $post_id));

        if ($featured_content === '') {
            continue;
        }

        $editorial_author_id = movies_theme_get_editorial_author_id($post_id);

        if ($editorial_author_id <= 0) {
            $editorial_author_id = (int) $movie->post_author;
        }

        $logged_timestamp = (int) get_post_time('U', true, $movie);

        update_post_meta($post_id, '_movies_theme_editorial_logged_at', $logged_timestamp > 0 ? $logged_timestamp : time());
        update_post_meta($post_id, '_movies_theme_editorial_logged_author', $editorial_author_id);
    }

    update_option('movies_theme_editorial_activity_backfilled', 1, false);
}, 20);

add_filter('option_aettaec_options', function ($options) {
    if (is_admin()) {
        return $options;
    }

    if (!is_array($options)) {
        $options = [];
    }

    $options['use_css'] = 0;

    return $options;
});

add_action('admin_post_nopriv_movies_theme_contribution_form', 'movies_theme_handle_contribution_form');
add_action('admin_post_movies_theme_contribution_form', 'movies_theme_handle_contribution_form');
add_action('admin_post_nopriv_movies_theme_mailing_signup', 'movies_theme_handle_mailing_signup');
add_action('admin_post_movies_theme_mailing_signup', 'movies_theme_handle_mailing_signup');
add_action('admin_post_movies_theme_send_mailing_post', 'movies_theme_handle_send_mailing_post');

function movies_theme_handle_contribution_form(): void
{
    $redirect_to = isset($_POST['redirect_to'])
        ? esc_url_raw(wp_unslash($_POST['redirect_to']))
        : home_url('/');

    if (!wp_verify_nonce(
        isset($_POST['movies_theme_contribution_nonce']) ? sanitize_text_field(wp_unslash($_POST['movies_theme_contribution_nonce'])) : '',
        'movies_theme_contribution_form'
    )) {
        wp_safe_redirect(add_query_arg('signup_status', 'error', $redirect_to));
        exit;
    }

    $honeypot = isset($_POST['company']) ? trim((string) wp_unslash($_POST['company'])) : '';

    if ($honeypot !== '') {
        wp_safe_redirect(add_query_arg('signup_status', 'success', $redirect_to));
        exit;
    }

    $topic = isset($_POST['contribution_topic'])
        ? sanitize_text_field(wp_unslash($_POST['contribution_topic']))
        : '';
    $name = isset($_POST['contribution_name'])
        ? trim(sanitize_text_field(wp_unslash($_POST['contribution_name'])))
        : '';
    $message = isset($_POST['contribution_message'])
        ? trim(sanitize_textarea_field(wp_unslash($_POST['contribution_message'])))
        : '';
    $allowed_topics = [
        'write_for_site' => 'I want to be a contributor',
        'movie_recommendation' => 'Movie Recommendation',
        'movie_spotlight_request' => 'Movie Spotlight Request',
        'general_inquiry' => 'General Inquiry',
    ];

    if (!isset($allowed_topics[$topic]) || $message === '') {
        wp_safe_redirect(add_query_arg('signup_status', 'error', $redirect_to));
        exit;
    }

    $subject = sprintf('Movies of the Month form: %s', $allowed_topics[$topic]);
    $body = implode("\n\n", [
        'Topic: ' . $allowed_topics[$topic],
        'Name: ' . ($name !== '' ? $name : 'Not provided'),
        'Message:',
        $message,
    ]);
    $sent = wp_mail('nashafoster@gmail.com', $subject, $body, [
        'Content-Type: text/plain; charset=UTF-8',
    ]);

    wp_safe_redirect(add_query_arg('signup_status', $sent ? 'success' : 'error', $redirect_to));
    exit;
}

function movies_theme_send_mailing_signup_confirmation(string $email): bool
{
    $enabled_setting = movies_theme_get_theme_setting('mailing_confirmation_enabled');
    $is_enabled = !in_array($enabled_setting, [0, '0', false, 'false'], true);

    if (!$is_enabled || !is_email($email)) {
        return false;
    }

    $subject = movies_theme_get_theme_setting('mailing_confirmation_subject');
    $body = movies_theme_get_theme_setting('mailing_confirmation_body');

    $subject = is_string($subject) && trim($subject) !== ''
        ? trim($subject)
        : 'Thank you for subscribing to Movies of the Month!';
    $body = is_string($body) && trim($body) !== ''
        ? trim($body)
        : "Thank you for subscribing to Movies of the Month!\n\nWe'll keep you posted with fun updates as we grow.\n\nWe promise not to spam you, and we'll never sell your data.";

    $body = str_replace('{email}', $email, $body);

    return wp_mail($email, $subject, $body, [
        'Content-Type: text/plain; charset=UTF-8',
    ]);
}

function movies_theme_handle_send_mailing_post(): void
{
    $post_id = isset($_GET['post_id']) ? (int) wp_unslash($_GET['post_id']) : 0;
    $redirect_to = $post_id > 0
        ? admin_url('post.php?post=' . $post_id . '&action=edit')
        : admin_url('edit.php?post_type=mailing_post');

    if (
        $post_id <= 0
        || !current_user_can('edit_post', $post_id)
        || !wp_verify_nonce(
            isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '',
            'movies_theme_send_mailing_post_' . $post_id
        )
    ) {
        wp_safe_redirect(add_query_arg('mailing_post_status', 'permission', $redirect_to));
        exit;
    }

    $subject = movies_theme_get_mailing_post_subject($post_id);
    $body = movies_theme_get_mailing_post_body($post_id);

    if ($subject === '' || $body === '') {
        wp_safe_redirect(add_query_arg('mailing_post_status', 'empty', $redirect_to));
        exit;
    }

    $emails = movies_theme_get_mailing_signup_emails();

    if ($emails === []) {
        wp_safe_redirect(add_query_arg('mailing_post_status', 'no_recipients', $redirect_to));
        exit;
    }

    $sent_count = 0;

    foreach ($emails as $email) {
        if (!is_string($email) || !is_email($email)) {
            continue;
        }

        $sent = wp_mail($email, $subject, $body, [
            'Content-Type: text/html; charset=UTF-8',
        ]);

        if ($sent) {
            $sent_count++;
        }
    }

    update_post_meta($post_id, '_movies_theme_mailing_post_sent_at', time());
    update_post_meta($post_id, '_movies_theme_mailing_post_sent_count', $sent_count);

    wp_safe_redirect(add_query_arg('mailing_post_status', 'sent', $redirect_to));
    exit;
}

function movies_theme_handle_mailing_signup(): void
{
    $redirect_to = isset($_POST['redirect_to'])
        ? esc_url_raw(wp_unslash($_POST['redirect_to']))
        : home_url('/');

    if (!wp_verify_nonce(
        isset($_POST['movies_theme_mailing_signup_nonce']) ? sanitize_text_field(wp_unslash($_POST['movies_theme_mailing_signup_nonce'])) : '',
        'movies_theme_mailing_signup'
    )) {
        wp_safe_redirect(add_query_arg('mailing_signup_status', 'error', $redirect_to));
        exit;
    }

    $honeypot = isset($_POST['company']) ? trim((string) wp_unslash($_POST['company'])) : '';

    if ($honeypot !== '') {
        wp_safe_redirect(add_query_arg('mailing_signup_status', 'success', $redirect_to));
        exit;
    }

    $email = isset($_POST['email'])
        ? sanitize_email(wp_unslash($_POST['email']))
        : '';

    if ($email === '' || !is_email($email)) {
        wp_safe_redirect(add_query_arg('mailing_signup_status', 'invalid', $redirect_to));
        exit;
    }

    $existing_signups = get_posts([
        'post_type' => 'mailing_signup',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_query' => [
            [
                'key' => '_movies_theme_mailing_signup_email',
                'value' => $email,
            ],
        ],
    ]);

    if ($existing_signups !== []) {
        wp_safe_redirect(add_query_arg('mailing_signup_status', 'exists', $redirect_to));
        exit;
    }

    $signup_id = wp_insert_post([
        'post_type' => 'mailing_signup',
        'post_status' => 'publish',
        'post_title' => $email,
    ], true);

    if ($signup_id instanceof WP_Error || $signup_id <= 0) {
        wp_safe_redirect(add_query_arg('mailing_signup_status', 'error', $redirect_to));
        exit;
    }

    update_post_meta($signup_id, '_movies_theme_mailing_signup_email', $email);
    movies_theme_send_mailing_signup_confirmation($email);

    wp_safe_redirect(add_query_arg('mailing_signup_status', 'success', $redirect_to));
    exit;
}
