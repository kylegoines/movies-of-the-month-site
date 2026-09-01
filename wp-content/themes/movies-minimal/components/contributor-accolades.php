<?php
$badges = $args['badges'] ?? [];

if (!is_array($badges) || $badges === []) {
    return;
}
?>

<ul class="contributor-accolades" aria-label="Contributor collections">
  <?php foreach ($badges as $badge_index => $badge) : ?>
    <?php
    if (!is_array($badge)) {
        continue;
    }

    $name = trim((string) ($badge['name'] ?? ''));
    $svg_url = esc_url_raw((string) ($badge['svg_url'] ?? ''));
    $color = sanitize_hex_color((string) ($badge['color'] ?? ''));
    $size = absint($badge['size'] ?? 25);
    $movie_title = trim((string) ($badge['movie_title'] ?? ''));
    $movie_url = (string) ($badge['movie_url'] ?? '');

    if ($name === '' || $svg_url === '' || $movie_url === '') {
        continue;
    }

    $color = is_string($color) && $color !== '' ? $color : '#111111';
    $size = $size > 0 ? max(10, min(100, $size)) : 25;
    $icon_style = sprintf(
        '--movie-badge-color: %1$s; --movie-badge-icon: url(%2$s); --movie-badge-size: %3$dpx;',
        $color,
        wp_json_encode($svg_url),
        $size
    );
    $month_title = preg_match('/\bmonth$/i', $name) === 1
        ? $name
        : $name . ' month';
    $tooltip_text = sprintf('Contributor to the %s.', $month_title);
    $accessible_label = $movie_title !== ''
        ? sprintf('%1$s collection. View %2$s.', $name, $movie_title)
        : sprintf('%s collection.', $name);
    $tooltip_id = sprintf(
        'contributor-collection-tooltip-%1$d-%2$d',
        (int) ($badge['term_id'] ?? 0),
        (int) $badge_index
    );
    ?>
    <li class="contributor-accolade" style="<?php echo esc_attr($icon_style); ?>">
      <a
        class="contributor-accolade__link"
        href="<?php echo esc_url($movie_url); ?>"
        aria-label="<?php echo esc_attr($accessible_label); ?>"
        aria-describedby="<?php echo esc_attr($tooltip_id); ?>"
      >
        <span class="contributor-accolade__icon" aria-hidden="true"></span>
        <span class="contributor-accolade__label"><?php echo esc_html($name); ?></span>
        <span class="contributor-accolade__tooltip" id="<?php echo esc_attr($tooltip_id); ?>" role="tooltip">
          <?php echo esc_html($tooltip_text); ?>
        </span>
      </a>
    </li>
  <?php endforeach; ?>
</ul>
