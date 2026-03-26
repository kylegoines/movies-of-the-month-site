<?php
get_header();

$filter_page_taglines = [
    'Movies are reel-y great',
    'Films are frame-tastic',
    'Cinema is a screen dream',
    'Movies are scene-sational',
    'Cinema is pop-corny perfection',
    'Movies are the reel deal',
    'Cinema is un-flick-gettable',
];

$scale_label_config = movies_theme_get_scale_label_config();
$movie_filter_keys = ['funny', 'scary', 'sadness', 'pacing'];
$selected_movie_filters = [];
$movie_meta_query = [];

foreach ($movie_filter_keys as $movie_filter_key) {
    $selected_value = isset($_GET[$movie_filter_key]) ? sanitize_text_field(wp_unslash($_GET[$movie_filter_key])) : '';
    $is_valid_value = $selected_value === '' || array_key_exists($selected_value, $scale_label_config[$movie_filter_key]);

    $selected_movie_filters[$movie_filter_key] = $is_valid_value ? $selected_value : '';

    if ($selected_movie_filters[$movie_filter_key] === '') {
        continue;
    }

    $movie_meta_query[] = [
        'key' => $movie_filter_key,
        'value' => $selected_movie_filters[$movie_filter_key],
        'compare' => '=',
    ];
}

$filter_page_tagline = $filter_page_taglines[array_rand($filter_page_taglines)];
$movies_query_args = [
    'post_type' => 'movies',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
];

if ($movie_meta_query !== []) {
    $movies_query_args['meta_query'] = $movie_meta_query;
}

$movies_query = new WP_Query($movies_query_args);
?>

<main class="mx-auto max-w-[1000px] px-6 py-16 md:px-8 md:py-24">
  <?php get_template_part('header/site', 'header'); ?>

  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <article class="mt-[56px]">
        <header>
          <div class="relative flex">
            <h2 class="theme-strong shrink-0 text-3xl tracking-[-0.05em] md:text-5xl">
              <span class="opacity-90"><?php echo esc_html($filter_page_tagline); ?></span>
            </h2>
            <div class="accent-rule mt-auto ml-7 h-[3px] w-full"></div>
          </div>
        </header>

        <?php if (get_the_content() !== '') : ?>
          <div class="post-content mt-8">
            <?php the_content(); ?>
          </div>
        <?php endif; ?>

        <section class="mt-12">
          <form action="<?php echo esc_url(get_permalink()); ?>" class="grid gap-5 md:grid-cols-2 xl:grid-cols-[repeat(4,minmax(0,1fr))_auto] xl:items-end" method="get">
            <?php if (is_page() && !get_option('permalink_structure')) : ?>
              <input type="hidden" name="page_id" value="<?php echo esc_attr((string) get_the_ID()); ?>">
            <?php endif; ?>

            <?php foreach ($movie_filter_keys as $movie_filter_key) : ?>
              <label class="block">
                <span class="theme-muted mb-2 block text-xs font-bold uppercase tracking-[0.18em]">
                  <?php echo esc_html(ucfirst($movie_filter_key)); ?>
                </span>
                <select class="theme-border theme-body theme-surface w-full border px-4 py-3" name="<?php echo esc_attr($movie_filter_key); ?>">
                  <option value="">Any</option>
                  <?php foreach ($scale_label_config[$movie_filter_key] as $scale_value => $scale_label) : ?>
                    <?php if ($scale_value === '0') : ?>
                      <?php continue; ?>
                    <?php endif; ?>
                    <option value="<?php echo esc_attr($scale_value); ?>" <?php selected($selected_movie_filters[$movie_filter_key], $scale_value); ?>>
                      <?php echo esc_html($scale_label); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </label>
            <?php endforeach; ?>

            <div class="flex gap-3 xl:justify-end">
              <button class="theme-strong theme-border cursor-pointer border px-5 py-3 font-bold transition-opacity hover:opacity-70" type="submit">
                Filter
              </button>
            </div>
          </form>
        </section>

        <?php if ($movies_query->have_posts()) : ?>
          <section class="mt-12 grid grid-cols-2 gap-x-6 gap-y-10 md:grid-cols-3 xl:grid-cols-5">
            <?php while ($movies_query->have_posts()) : ?>
              <?php
              $movies_query->the_post();
              $subtitle = movies_theme_get_subtitle(get_the_ID());
              $poster = get_the_post_thumbnail(get_the_ID(), 'large', [
                  'class' => 'h-auto w-full object-cover',
                  'loading' => 'lazy',
              ]);
              ?>
              <article>
                <a class="block no-underline transition-opacity hover:opacity-70" href="<?php the_permalink(); ?>">
                  <?php if ($poster !== '') : ?>
                    <div class="poster-frame theme-surface">
                      <?php echo $poster; ?>
                    </div>
                  <?php endif; ?>

                  <h3 class="theme-strong mt-4 text-xl tracking-[-0.04em]">
                    <?php the_title(); ?>
                  </h3>

                  <?php if ($subtitle !== '') : ?>
                    <p class="theme-body mt-2 text-base font-bold">
                      <?php echo esc_html($subtitle); ?>
                    </p>
                  <?php endif; ?>
                </a>
              </article>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
          </section>
        <?php else : ?>
          <p class="theme-muted mt-12 py-6 text-sm uppercase tracking-[0.18em]">No movies yet.</p>
        <?php endif; ?>
      </article>
    <?php endwhile; ?>
  <?php endif; ?>
</main>

<?php
get_footer();
?>
