<?php
get_header();

$author_id = (int) get_queried_object_id();
$author_name = movies_theme_get_author_name($author_id);
$author_custom_bio = function_exists('get_field')
    ? trim((string) get_field('bio', 'user_' . $author_id))
    : '';
$author_bio = $author_custom_bio !== ''
    ? $author_custom_bio
    : trim((string) get_the_author_meta('description', $author_id));
$author_categories = movies_theme_get_author_movie_categories($author_id);
$author_category_stats = movies_theme_get_author_movie_category_stats($author_id);
$author_user = get_userdata($author_id);
$author_roles = $author_user instanceof WP_User ? array_values($author_user->roles) : [];
$author_role_badges = [];
$author_key = 'user_' . $author_id;
$author_links = [
    'Website' => function_exists('get_field') ? trim((string) get_field('personal_website', $author_key)) : '',
    'Twitter' => function_exists('get_field') ? trim((string) get_field('twitter', $author_key)) : '',
    'Bluesky' => function_exists('get_field') ? trim((string) get_field('bluesky', $author_key)) : '',
    'Letterboxd' => function_exists('get_field') ? trim((string) get_field('letterboxd', $author_key)) : '',
];
$author_links = array_filter($author_links, static fn(string $url): bool => $url !== '');
$active_category = sanitize_title((string) get_query_var('movie_category'));
$active_category_term = null;
$author_possessive_name = preg_match('/s$/i', $author_name)
    ? $author_name . "'"
    : $author_name . "'s";

$role_badge_config = [
    'administrator' => [
        'label' => 'Administrator',
        'style' => 'background:#f3f4f6;color:#374151;border-color:#d1d5db;',
    ],
    'editor' => [
        'label' => 'Editor',
        'style' => 'background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;',
    ],
    'core_contributor' => [
        'label' => 'Core Contributor',
        'style' => 'background:#f0fdf4;color:#166534;border-color:#bbf7d0;',
    ],
    'contributor' => [
        'label' => 'Contributor',
        'style' => 'background:#fffbeb;color:#92400e;border-color:#fde68a;',
    ],
];

foreach ($author_roles as $author_role) {
    if (isset($role_badge_config[$author_role])) {
        $author_role_badges[] = $role_badge_config[$author_role];
    }
}

if ($active_category !== '') {
    foreach ($author_categories as $author_category) {
        if ($author_category->slug === $active_category) {
            $active_category_term = $author_category;
            break;
        }
    }
}

$author_heading = $active_category_term instanceof WP_Term
    ? sprintf('%s %s picks', $author_possessive_name, strtolower($active_category_term->name))
    : $author_name;
?>

<?php get_template_part('header/site', 'header'); ?>

<main class="mx-auto mt-10 min-h-[calc(100vh-114px)] md:max-w-[720px] lg:max-w-[1000px] px-[32px] pb-[50px] sm:min-h-[calc(100vh-154px)] sm:pb-[80px] md:mt-12 lg:min-h-[calc(100vh-232px)] lg:pb-[100px]">
  <header class="page-header border-4 p-4">
    <h1 class="page-header__title theme-strong rhythm-sm lg:rhythm-md">
      <?php echo esc_html($author_heading); ?>
    </h1>

    <?php if ($author_role_badges !== [] || $author_links !== []) : ?>
      <div class="rhythm-sm flex flex-wrap items-center gap-x-3 gap-y-2">
        <?php foreach ($author_role_badges as $author_role_badge) : ?>
          <span
            class="inline-flex items-center rounded-full border px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.12em]"
            style="<?php echo esc_attr($author_role_badge['style']); ?>"
          >
            <?php echo esc_html($author_role_badge['label']); ?>
          </span>
        <?php endforeach; ?>

        <?php if ($author_links !== []) : ?>
          <ul class="flex flex-wrap items-center gap-x-3 gap-y-2 text-sm">
            <?php foreach ($author_links as $link_label => $link_url) : ?>
              <li class="theme-body flex items-center gap-3">
                <a class="theme-strong font-bold no-underline" href="<?php echo esc_url($link_url); ?>" target="_blank" rel="noreferrer">
                  <?php echo esc_html($link_label); ?>
                </a>
                <?php if ($link_label !== array_key_last($author_links)) : ?>
                  <span class="theme-strong text-xs" aria-hidden="true">•</span>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if ($author_bio !== '' || $author_category_stats['categories'] !== []) : ?>
      <aside class="theme-border rhythm-md grid gap-8 pt-4 md:grid-cols-2 md:gap-10">
        <?php if ($author_category_stats['categories'] !== []) : ?>
          <div class="<?php echo $author_bio !== '' ? 'order-2 md:order-1' : ''; ?>">
            <p class="theme-muted text-xs font-bold uppercase tracking-[0.18em] rhythm-sm lg:rhythm-md">Genre Breakdown</p>
            <ul class="rhythm-sm space-y-3">
              <?php foreach ($author_category_stats['categories'] as $category_stat) : ?>
                <li class="theme-body flex items-baseline justify-between gap-4 text-sm relative">
                  <span class="theme-strong font-bold bg-white z-[2] pr-[8px]"><?php echo esc_html($category_stat['term']->name); ?></span>
                  <div class="absolute h-[1px] w-full bg-black top-[10px]"></div>
                  <span class="bg-white z-[2] pl-[8px]"><?php echo esc_html($category_stat['percentage']); ?>%</span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php if ($author_bio !== '') : ?>
        <div class="<?php echo $author_category_stats['categories'] !== [] ? 'order-1 md:order-2' : ''; ?>">
          <p class="theme-muted text-xs font-bold uppercase tracking-[0.18em] rhythm-sm lg:rhythm-md">About</p>
          <p class="theme-body rhythm-sm text-sm leading-6">
            <?php echo esc_html($author_bio); ?>
          </p>
        </div>
        <?php endif; ?>
      </aside>
    <?php endif; ?>
  </header>

  <?php if ($author_categories !== []) : ?>
    <nav class="rhythm-md flex flex-wrap items-center gap-6">
      <a
        class="text-sm font-bold uppercase tracking-[0.08em] transition-opacity hover:opacity-70 <?php echo $active_category === '' ? 'px-3 py-2' : 'theme-strong'; ?>"
        style="<?php echo $active_category === '' ? esc_attr('background: var(--color-strong); color: var(--color-background);') : ''; ?>"
        href="<?php echo esc_url(get_author_posts_url($author_id)); ?>"
      >
        All
      </a>
      <?php foreach ($author_categories as $category) : ?>
        <?php
        $is_active = $active_category === $category->slug;
        $filter_url = add_query_arg('movie_category', $category->slug, get_author_posts_url($author_id));
        ?>
        <a
          class="text-sm font-bold uppercase tracking-[0.08em] transition-opacity hover:opacity-70 <?php echo $is_active ? 'px-3 py-2' : 'theme-strong'; ?>"
          style="<?php echo $is_active ? esc_attr('background: var(--color-strong); color: var(--color-background);') : ''; ?>"
          href="<?php echo esc_url($filter_url); ?>"
        >
          <?php echo esc_html($category->name); ?>
        </a>
      <?php endforeach; ?>
    </nav>
  <?php endif; ?>

  <?php if (have_posts()) : ?>
    <?php
    get_template_part('components/movie-grid-full', null, [
        'movies' => $wp_query->posts,
        'grid_classes' => 'rhythm-lg grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4',
    ]);
    ?>
  <?php else : ?>
    <p class="theme-muted py-6 text-sm uppercase tracking-[0.18em]">No movies yet.</p>
  <?php endif; ?>
</main>

<?php
get_footer();
?>
