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
?>

<header class="flex pb-8">
  <h1 class="shrink-0">
    <a class="inline-block no-underline relative" href="<?php echo esc_url(
        home_url('/'),
    ); ?>">
    <!-- <div class="h-[15px] w-[15px] bg-black rounded-4xl absolute top-[60px] left-[23px]"></div> -->
      <img
        class="h-auto w-full max-w-[280px] md:max-w-[360px] relative top-[7px]"
        src="<?php echo esc_url(
            get_template_directory_uri() . '/images/logo1.svg',
        ); ?>"
        alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
      >
      <span class="sr-only"><?php bloginfo('name'); ?></span>
    </a>
  </h1>
  <div class="ml-6 mt-auto w-full">
    <ul class="hidden list-none justify-between pl-17 pr-2 text-xl font-bold text-black sm:flex">
      <li>
        <a class="transition-opacity hover:opacity-70" href="<?php echo esc_url($latest_collection_url); ?>">
          This Month
        </a>
      </li>
      <li>
        <a class="transition-opacity hover:opacity-70" href="<?php echo esc_url($past_months_url); ?>">
          Past Months
        </a>
      </li>
      <li>
        <span class="cursor-default">About</span>
      </li>
      <li>
        <span class="cursor-default">Sign Up</span>
      </li>
    </ul>
    <div class="mt-auto h-[3px] w-full bg-black"></div>
  </div>
</header>
