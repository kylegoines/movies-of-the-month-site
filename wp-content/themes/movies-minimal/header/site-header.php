<?php
$latest_collection = get_posts([
    'post_type' => 'collection',
    'posts_per_page' => 1,
    'fields' => 'ids',
    'no_found_rows' => true,
]);

$latest_collection_url = $latest_collection !== []
    ? get_permalink($latest_collection[0])
    : home_url('/');

$past_months_url = add_query_arg('view', 'past-months', home_url('/'));
$current_view = isset($_GET['view']) ? sanitize_key(wp_unslash($_GET['view'])) : '';
$is_this_month = is_singular('collection') && $latest_collection !== [] && get_queried_object_id() === (int) $latest_collection[0];
$is_past_months = is_home() && $current_view === 'past-months';
$is_browse = is_page('filter');
$is_contributors = is_page('contributors');
$marquee_state = movies_theme_get_site_title_marquee_state();
$marquee_is_paused = (bool) $marquee_state['is_paused'];
$marquee_progress = (float) $marquee_state['progress'];
$marquee_button_label = $marquee_is_paused ? 'Play animation' : 'Pause animation';
$marquee_track_style = '--site-title-marquee-progress: ' . number_format($marquee_progress, 5, '.', '') . ';';

if ($marquee_is_paused) {
    $marquee_track_style .= ' --site-title-marquee-transform: translateX(calc(' . number_format($marquee_progress, 5, '.', '') . ' * -20%));';
}
?>

<header class="site-title-marquee-shell pt-[12px]">
  <button
    class="site-mobile-nav__toggle inline-flex lg:hidden"
    type="button"
    data-site-mobile-nav-toggle
    aria-expanded="false"
    aria-controls="site-mobile-nav"
    aria-label="Open navigation"
  >
    <span class="site-mobile-nav__toggle-icon" aria-hidden="true">
      <span></span>
      <span></span>
      <span></span>
    </span>
  </button>
  <a class="site-title-marquee no-underline" href="<?php echo esc_url(home_url('/')); ?>">
    <span class="sr-only"><?php bloginfo('name'); ?></span>
    <div
      class="site-title-marquee__track"
      aria-hidden="true"
      data-site-title-marquee-track
      data-state="<?php echo $marquee_is_paused ? 'paused' : 'playing'; ?>"
      data-hover-paused="false"
      data-marquee-progress="<?php echo esc_attr(number_format($marquee_progress, 5, '.', '')); ?>"
      style="<?php echo esc_attr($marquee_track_style); ?>"
    >
      <span>Movies of the Month</span>
      <span class="site-title-marquee__dot"></span>
      <span>Movies of the Month</span>
      <span class="site-title-marquee__dot"></span>
      <span>Movies of the Month</span>
      <span class="site-title-marquee__dot"></span>
      <span>Movies of the Month</span>
      <span class="site-title-marquee__dot"></span>
      <span>Movies of the Month</span>
      <span class="site-title-marquee__dot"></span>
    </div>
  </a>
  <button
    class="site-title-marquee__toggle hidden lg:inline-flex"
    type="button"
    data-site-title-marquee-toggle
    aria-pressed="<?php echo $marquee_is_paused ? 'true' : 'false'; ?>"
    aria-label="<?php echo esc_attr($marquee_button_label); ?>"
  >
    <?php echo esc_html($marquee_button_label); ?>
  </button>
  <div class="w-screen ">
    <ul class="theme-strong mx-auto hidden max-w-[1000px] list-none justify-between px-8 text-xl font-bold lg:flex lg:py-6">
      <li>
        <a
          class="transition-opacity hover:opacity-70 <?php echo $is_this_month ? 'text-[var(--hidden-gem-accent)]' : ''; ?>"
          href="<?php echo esc_url($latest_collection_url); ?>"
        >
          This Month
        </a>
      </li>
      <li>
        <a
          class="transition-opacity hover:opacity-70 <?php echo $is_past_months ? 'text-[var(--hidden-gem-accent)]' : ''; ?>"
          href="<?php echo esc_url($past_months_url); ?>"
        >
          Past Months
        </a>
      </li>
      <li>
        <a
          class="transition-opacity hover:opacity-70 <?php echo $is_browse ? 'text-[var(--hidden-gem-accent)]' : ''; ?>"
          href="<?php echo esc_url(home_url('/filter/')); ?>"
        >
          Browse
        </a>
      </li>
      <li>
        <a
          class="transition-opacity hover:opacity-70 <?php echo $is_contributors ? 'text-[var(--hidden-gem-accent)]' : ''; ?>"
          href="<?php echo esc_url(home_url('/contributors/')); ?>"
        >
          Contributors
        </a>
      </li>
    </ul>
    <div class="accent-rule h-[3px] w-full"></div>
  </div>

  <div
    class="site-mobile-nav h-[calc(100vh-71px)] sm:h-[calc(100vh-107px)] lg:hidden"
    id="site-mobile-nav"
    data-site-mobile-nav
    aria-hidden="true"
  >


    <nav class="site-mobile-nav__body" aria-label="Mobile">
      <a
        class="site-mobile-nav__link <?php echo $is_this_month ? 'text-[var(--hidden-gem-accent)]' : ''; ?>"
        href="<?php echo esc_url($latest_collection_url); ?>"
      >
        This Month
      </a>
      <a
        class="site-mobile-nav__link <?php echo $is_past_months ? 'text-[var(--hidden-gem-accent)]' : ''; ?>"
        href="<?php echo esc_url($past_months_url); ?>"
      >
        Past Months
      </a>
      <a
        class="site-mobile-nav__link <?php echo $is_browse ? 'text-[var(--hidden-gem-accent)]' : ''; ?>"
        href="<?php echo esc_url(home_url('/filter/')); ?>"
      >
        Browse
      </a>
      <a
        class="site-mobile-nav__link <?php echo $is_contributors ? 'text-[var(--hidden-gem-accent)]' : ''; ?>"
        href="<?php echo esc_url(home_url('/contributors/')); ?>"
      >
        Contributors
      </a>
    </nav>
  </div>
</header>
