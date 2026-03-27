<?php
get_header();

$contributors = get_users([
    'fields' => 'all',
]);

$contributors = array_values(array_filter($contributors, static function (WP_User $user): bool {
    $user_key = 'user_' . $user->ID;

    $profile_image_id = function_exists('get_field')
        ? (int) get_field('profile_image', $user_key)
        : 0;
    $custom_bio = function_exists('get_field')
        ? trim((string) get_field('bio', $user_key))
        : '';
    $website = function_exists('get_field')
        ? trim((string) get_field('personal_website', $user_key))
        : '';
    $twitter = function_exists('get_field')
        ? trim((string) get_field('twitter', $user_key))
        : '';
    $bluesky = function_exists('get_field')
        ? trim((string) get_field('bluesky', $user_key))
        : '';
    $letterboxd = function_exists('get_field')
        ? trim((string) get_field('letterboxd', $user_key))
        : '';
    $native_bio = trim((string) get_the_author_meta('description', $user->ID));

    $has_profile_fields = $profile_image_id > 0
        || $custom_bio !== ''
        || $native_bio !== ''
        || $website !== ''
        || $twitter !== ''
        || $bluesky !== ''
        || $letterboxd !== '';

    $has_movies = count_user_posts($user->ID, 'movies', true) > 0;

    return $has_profile_fields || $has_movies;
}));

usort($contributors, static function (WP_User $left, WP_User $right): int {
    $left_count = count_user_posts($left->ID, 'movies', true);
    $right_count = count_user_posts($right->ID, 'movies', true);

    if ($left_count !== $right_count) {
        return $right_count <=> $left_count;
    }

    return strcasecmp(
        movies_theme_get_author_name($left->ID),
        movies_theme_get_author_name($right->ID)
    );
});
?>

<main class="mx-auto max-w-[1000px] px-6 py-16 md:px-8 md:py-24">
  <?php get_template_part('header/site', 'header'); ?>

  <section class="mt-[56px]">
    <div class="flex">
      <h1 class="theme-strong shrink-0 text-4xl tracking-[-0.06em] md:text-6xl">Contributors</h1>
      <div class="accent-rule mt-auto ml-7 h-[3px] w-full"></div>
    </div>

    <?php if ($contributors !== []) : ?>
      <div class="mt-10 space-y-12">
        <?php foreach ($contributors as $contributor) : ?>
          <?php
          $contributor_id = (int) $contributor->ID;
          $contributor_key = 'user_' . $contributor_id;
          $contributor_name = movies_theme_get_author_name($contributor_id);
          $contributor_count = count_user_posts($contributor_id, 'movies', true);
          $contributor_image_id = function_exists('get_field')
              ? (int) get_field('profile_image', $contributor_key)
              : 0;
          $contributor_bio = function_exists('get_field')
              ? trim((string) get_field('bio', $contributor_key))
              : '';
          $contributor_bio = $contributor_bio !== ''
              ? $contributor_bio
              : trim((string) get_the_author_meta('description', $contributor_id));
          $contributor_links = [
              'Personal Website' => function_exists('get_field') ? trim((string) get_field('personal_website', $contributor_key)) : '',
              'Twitter' => function_exists('get_field') ? trim((string) get_field('twitter', $contributor_key)) : '',
              'Bluesky' => function_exists('get_field') ? trim((string) get_field('bluesky', $contributor_key)) : '',
              'Letterboxd' => function_exists('get_field') ? trim((string) get_field('letterboxd', $contributor_key)) : '',
          ];
          $contributor_links = array_filter($contributor_links, static fn(string $url): bool => $url !== '');
          $contributor_image = $contributor_image_id > 0
              ? wp_get_attachment_image($contributor_image_id, 'medium', false, [
                  'class' => 'h-full w-full object-cover',
                  'loading' => 'lazy',
              ])
              : '';
          ?>
          <article class="grid gap-6 md:grid-cols-[140px_minmax(0,1fr)] md:items-start">
            <div class="poster-frame theme-surface">
              <div class="relative z-[2] aspect-square overflow-hidden">
                <?php if ($contributor_image !== '') : ?>
                  <?php echo $contributor_image; ?>
                <?php else : ?>
                  <div class="h-full w-full bg-neutral-300"></div>
                <?php endif; ?>
              </div>
            </div>

            <div class="min-w-0">
              <h2 class="theme-strong flex flex-wrap items-baseline gap-x-3 gap-y-1 text-3xl tracking-[-0.05em] md:text-4xl">
                <a class="no-underline" href="<?php echo esc_url(get_author_posts_url($contributor_id)); ?>">
                  <?php echo esc_html($contributor_name); ?>
                </a>
                <span class="theme-muted text-base tracking-normal md:text-lg">
                  <?php echo esc_html(sprintf(
                      _n('%s contribution', '%s contributions', $contributor_count, 'movies-minimal'),
                      number_format_i18n($contributor_count)
                  )); ?>
                </span>
              </h2>

              <?php if ($contributor_bio !== '') : ?>
                <div class="post-content mt-4 max-w-[680px] text-base">
                  <?php echo wpautop(esc_html($contributor_bio)); ?>
                </div>
              <?php endif; ?>

              <?php if ($contributor_links !== []) : ?>
                <ul class="mt-5 flex flex-wrap items-center gap-x-3 gap-y-2 text-sm">
                  <?php foreach ($contributor_links as $link_label => $link_url) : ?>
                    <li class="theme-body flex items-center gap-3">
                      <a class="theme-strong font-bold no-underline" href="<?php echo esc_url($link_url); ?>" target="_blank" rel="noreferrer">
                        <?php echo esc_html($link_label); ?>
                      </a>
                      <?php if ($link_label !== array_key_last($contributor_links)) : ?>
                        <span class="theme-strong text-xs" aria-hidden="true">•</span>
                      <?php endif; ?>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else : ?>
      <p class="theme-muted mt-8 text-sm uppercase tracking-[0.18em]">No contributors yet.</p>
    <?php endif; ?>
  </section>
</main>

<?php
get_footer();
?>
