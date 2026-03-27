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

<header class="pb-8">
  <a class="site-title-marquee no-underline" href="<?php echo esc_url(home_url('/')); ?>">
    <span class="sr-only"><?php bloginfo('name'); ?></span>
    <div class="site-title-marquee__track" aria-hidden="true">
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

  <div class="mt-6 w-screen">
    <ul class="theme-strong hidden list-none justify-between text-xl font-bold sm:flex mx-auto max-w-[1000px] px-8">
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
</header>
