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
?>

<header class="pt-[15px] pb-8">
  <button
    class="site-mobile-nav__toggle"
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
    <div class="site-title-marquee__track" aria-hidden="true" data-site-title-marquee-track>
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
    class="site-title-marquee__toggle theme-strong theme-border"
    type="button"
    data-site-title-marquee-toggle
    aria-pressed="false"
    aria-label="Pause marquee"
  >
    ||
  </button>
  <div class="w-screen ">
    <ul class="theme-strong mx-auto hidden max-w-[1000px] list-none justify-between px-8 text-xl font-bold sm:flex lg:py-6">
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
    <div class="accent-rule mt-3 h-[3px] w-full"></div>
  </div>

  <div
    class="site-mobile-nav"
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
