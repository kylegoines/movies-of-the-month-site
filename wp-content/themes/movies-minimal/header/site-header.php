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
$logo_markup = movies_theme_get_inline_svg(
    'images/logo1.svg',
    'h-auto w-full max-w-[280px] md:max-w-[360px] relative top-[7px]'
);
?>

<header class="flex pb-8">
  <h1 class="shrink-0">
    <a class="inline-block no-underline relative" href="<?php echo esc_url(
        home_url('/'),
    ); ?>">
    <div class="accent-rule mt-auto h-[3px] w-[47px] absolute bottom-[-7px]"></div>
    <!-- <div class="h-[15px] w-[15px] bg-black rounded-4xl absolute top-[60px] left-[23px]"></div> -->
      <?php if ($logo_markup !== '') : ?>
        <?php echo $logo_markup; ?>
      <?php endif; ?>
      <span class="sr-only"><?php bloginfo('name'); ?></span>
    </a>
  </h1>
  <div class="ml-6 mt-auto w-full">
    <ul class="theme-strong hidden list-none justify-between pl-17 pr-2 text-xl font-bold sm:flex">
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
        <a class="transition-opacity hover:opacity-70" href="<?php echo esc_url(home_url('/filter/')); ?>">
          Filters
        </a>
      </li>
      <li>
        <button class="cursor-pointer transition-opacity hover:opacity-70" type="button" data-signup-jump>
          Sign Up
        </button>
      </li>
    </ul>
    <div class="accent-rule mt-auto h-[3px] w-full"></div>
  </div>
</header>
