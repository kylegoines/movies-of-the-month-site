<?php
get_header();

$author_id = (int) get_queried_object_id();
$author_name = (string) get_the_author_meta('display_name', $author_id);
$author_bio = trim((string) get_the_author_meta('description', $author_id));
$author_categories = movies_theme_get_author_movie_categories($author_id);
$author_category_stats = movies_theme_get_author_movie_category_stats($author_id);
$active_category = sanitize_title((string) get_query_var('movie_category'));
$active_category_term = null;
$author_possessive_name = preg_match('/s$/i', $author_name)
    ? $author_name . "'"
    : $author_name . "'s";

if ($active_category !== '') {
    foreach ($author_categories as $author_category) {
        if ($author_category->slug === $active_category) {
            $active_category_term = $author_category;
            break;
        }
    }
}

$author_heading = $active_category_term instanceof WP_Term
    ? sprintf('%s %s picks', $author_possessive_name, $active_category_term->name)
    : sprintf('%s picks', $author_possessive_name);
?>

<main class="mx-auto max-w-[1000px] px-6 py-16 md:px-8 md:py-24">
  <?php get_template_part('header/site', 'header'); ?>

  <header class="mt-[48px] grid gap-10 md:grid-cols-[minmax(0,1fr)_280px] md:items-start">
    <div>
      <h1 class="theme-strong text-4xl tracking-[-0.06em] md:text-6xl">
        <?php echo esc_html($author_heading); ?>
      </h1>
    </div>

    <?php if ($author_bio !== '' || $author_category_stats['categories'] !== []) : ?>
      <aside class="theme-border grid gap-8 border-t pt-4 md:pt-0">
        <?php if ($author_category_stats['categories'] !== []) : ?>
          <div>
            <p class="theme-muted text-xs font-bold uppercase tracking-[0.18em]">Genra Breakdown</p>
            <ul class="mt-3 space-y-3">
              <?php foreach ($author_category_stats['categories'] as $category_stat) : ?>
                <li class="theme-body flex items-baseline justify-between gap-4 text-sm">
                  <span class="theme-strong font-bold"><?php echo esc_html($category_stat['term']->name); ?></span>
                  <span><?php echo esc_html($category_stat['percentage']); ?>%</span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php if ($author_bio !== '') : ?>
        <div>
          <p class="theme-muted text-xs font-bold uppercase tracking-[0.18em]">About</p>
          <p class="theme-body mt-3 text-sm leading-6">
            <?php echo esc_html($author_bio); ?>
          </p>
        </div>
        <?php endif; ?>
      </aside>
    <?php endif; ?>
  </header>

  <?php if ($author_categories !== []) : ?>
    <div class="mt-8 h-1 w-full bg-black"></div>
    <nav class="mt-6 flex flex-wrap items-center gap-6">
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
    <section class="mt-10 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
      <?php while (have_posts()) : the_post(); ?>
        <?php
        $subtitle = movies_theme_get_subtitle(get_the_ID());
        $is_hidden_gem = movies_theme_is_hidden_gem(get_the_ID());
        $gem_badge = $is_hidden_gem
            ? movies_theme_get_inline_svg('images/gemsingle.svg', 'theme-gem-badge')
            : '';
        $poster = get_the_post_thumbnail(get_the_ID(), 'large', [
            'class' => 'h-auto w-full object-cover',
            'loading' => 'lazy',
        ]);
        ?>
        <article>
          <a class="movie-card block no-underline" href="<?php the_permalink(); ?>">
            <?php if ($poster !== '') : ?>
              <div class="poster-frame theme-surface <?php echo $is_hidden_gem ? 'poster-frame--hidden-gem' : ''; ?>">
                <?php echo $poster; ?>
              </div>
            <?php endif; ?>

            <h2 class="mt-4 flex items-center gap-2 text-xl tracking-[-0.04em] <?php echo $is_hidden_gem ? 'movie-title--hidden-gem' : 'theme-strong'; ?>">
              <span><?php the_title(); ?></span>
              <?php if ($gem_badge !== '') : ?>
                <span class="movie-title__gem"><?php echo $gem_badge; ?></span>
              <?php endif; ?>
            </h2>

            <?php if ($subtitle !== '') : ?>
              <p class="theme-body mt-2 text-base font-bold">
                <?php echo esc_html($subtitle); ?>
              </p>
            <?php endif; ?>
          </a>
        </article>
      <?php endwhile; ?>
    </section>
  <?php else : ?>
    <p class="theme-muted py-6 text-sm uppercase tracking-[0.18em]">No movies yet.</p>
  <?php endif; ?>
</main>

<?php
get_footer();
?>
