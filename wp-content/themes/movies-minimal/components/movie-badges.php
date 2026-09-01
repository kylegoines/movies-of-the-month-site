<?php
$badges = $args['badges'] ?? [];

if (!is_array($badges) || $badges === []) {
    return;
}
?>

<div class="movie-badges-field">
  <ul class="movie-badges" aria-label="Movie badges">
    <?php foreach ($badges as $badge_index => $badge) : ?>
      <?php
      if (!is_array($badge)) {
          continue;
      }

      $name = trim((string) ($badge['name'] ?? ''));
      $description = trim((string) ($badge['description'] ?? ''));
      $svg_url = esc_url_raw((string) ($badge['svg_url'] ?? ''));
      $color = sanitize_hex_color((string) ($badge['color'] ?? ''));
      $size = absint($badge['size'] ?? 25);

      if ($name === '' || $svg_url === '') {
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
      $tooltip_text = $description !== ''
          ? $description
          : sprintf('This movie was recommended for %s.', $name);
      $tooltip_id = sprintf(
          'movie-badge-tooltip-%1$d-%2$d',
          (int) ($badge['term_id'] ?? 0),
          (int) $badge_index
      );
      ?>
      <li
        class="movie-badge"
        style="<?php echo esc_attr($icon_style); ?>"
        tabindex="0"
        aria-label="<?php echo esc_attr($name . ' badge'); ?>"
        aria-describedby="<?php echo esc_attr($tooltip_id); ?>"
      >
        <span class="movie-badge__icon" aria-hidden="true"></span>
        <span class="movie-badge__label"><?php echo esc_html($name); ?></span>
        <span class="movie-badge__tooltip" id="<?php echo esc_attr($tooltip_id); ?>" role="tooltip">
          <?php echo esc_html($tooltip_text); ?>
        </span>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
