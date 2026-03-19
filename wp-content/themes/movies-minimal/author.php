<?php
get_header();

$author_id = (int) get_queried_object_id();
$author_bio = trim((string) get_the_author_meta('description', $author_id));
$author_categories = movies_minimal_get_author_movie_categories($author_id);
$author_category_stats = movies_minimal_get_author_movie_category_stats($author_id);
$active_category = sanitize_title((string) get_query_var('movie_category'));
?>

<main class="mx-auto max-w-[1000px] px-6 py-16 md:px-8 md:py-24">
  <?php get_template_part('header/site', 'header'); ?>

  <header class="mt-[48px] grid gap-10 md:grid-cols-[minmax(0,1fr)_280px] md:items-start">
    <div>
      <h1 class="text-4xl tracking-[-0.06em] text-black md:text-6xl">
        <?php echo esc_html(get_the_author()); ?>
      </h1>
      <?php if ($author_category_stats['categories'] !== []) : ?>
        <div class="mt-6">
          <p class="text-xs font-bold uppercase tracking-[0.18em] text-neutral-500">Category Mix</p>
          <ul class="mt-3 space-y-3">
            <?php foreach ($author_category_stats['categories'] as $category_stat) : ?>
              <li class="flex max-w-[280px] items-baseline justify-between gap-4 text-sm text-neutral-800">
                <span class="font-bold text-black"><?php echo esc_html($category_stat['term']->name); ?></span>
                <span><?php echo esc_html($category_stat['percentage']); ?>%</span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </div>

    <?php if ($author_bio !== '') : ?>
      <aside class="border-t border-black pt-4 md:pt-0 ">
        <div>
          <p class="text-xs font-bold uppercase tracking-[0.18em] text-neutral-500">About</p>
          <p class="mt-3 text-sm leading-6 text-neutral-800">
            <?php echo esc_html($author_bio); ?>
          </p>
        </div>
      </aside>
    <?php endif; ?>
  </header>

  <?php if ($author_categories !== []) : ?>
    <nav class="mt-8 flex flex-wrap gap-3">
      <a
        class="text-sm font-bold uppercase tracking-[0.08em] text-black transition-opacity hover:opacity-70 <?php echo $active_category === '' ? 'underline underline-offset-4' : ''; ?>"
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
          class="text-sm font-bold uppercase tracking-[0.08em] text-black transition-opacity hover:opacity-70 <?php echo $is_active ? 'underline underline-offset-4' : ''; ?>"
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
        $subtitle = movies_minimal_get_subtitle(get_the_ID());
        $poster = get_the_post_thumbnail(get_the_ID(), 'large', [
            'class' => 'h-auto w-full object-cover',
            'loading' => 'lazy',
        ]);
        ?>
        <article>
          <a class="block no-underline transition-opacity hover:opacity-70" href="<?php the_permalink(); ?>">
            <?php if ($poster !== '') : ?>
              <div class="overflow-hidden bg-neutral-100">
                <?php echo $poster; ?>
              </div>
            <?php endif; ?>

            <h2 class="mt-4 text-xl tracking-[-0.04em] text-black">
              <?php the_title(); ?>
            </h2>

            <?php if ($subtitle !== '') : ?>
              <p class="mt-2 text-base font-bold text-neutral-800">
                <?php echo esc_html($subtitle); ?>
              </p>
            <?php endif; ?>
          </a>
        </article>
      <?php endwhile; ?>
    </section>
  <?php else : ?>
    <p class="py-6 text-sm uppercase tracking-[0.18em] text-neutral-500">No movies yet.</p>
  <?php endif; ?>
</main>

<?php
get_footer();
?>
