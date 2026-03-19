<?php
$month_links = [];
$current_month = new DateTimeImmutable('first day of this month', wp_timezone());

for ($offset = 0; $offset < 3; $offset++) {
    $month = $current_month->modify("-{$offset} month");

    $month_links[] = [
        'label' => wp_date('F', $month->getTimestamp()),
        'url' => get_month_link($month->format('Y'), $month->format('m')),
    ];
}
?>

<header class="flex pb-8">
  <h1 class="shrink-0">
    <a class="inline-block no-underline" href="<?php echo esc_url(
        home_url('/'),
    ); ?>">
      <img
        class="h-auto w-full max-w-[280px] md:max-w-[360px]"
        src="<?php echo esc_url(
            get_template_directory_uri() . '/images/logo1.svg',
        ); ?>"
        alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
      >
      <span class="sr-only"><?php bloginfo('name'); ?></span>
    </a>
  </h1>
  <div class="ml-6 mt-auto w-full">
    <ul class="hidden list-none justify-between px-9 text-xl font-bold text-black sm:flex">
      <?php foreach ($month_links as $index => $month_link) : ?>
        <li class="<?php echo $index === 3 ? 'hidden md:list-item' : ''; ?>">
          <a
            class="transition-opacity hover:opacity-70"
            href="<?php echo esc_url($month_link['url']); ?>"
          >
            <?php echo esc_html($month_link['label']); ?>
          </a>
        </li>
      <?php endforeach; ?>
      <li>
        <a class="transition-opacity hover:opacity-70" href="<?php echo esc_url(home_url('/')); ?>">
          More...
        </a>
      </li>
    </ul>
    <div class="mt-auto h-[5px] w-full bg-black"></div>
  </div>
</header>
