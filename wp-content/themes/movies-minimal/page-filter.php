<?php
get_header();

$scale_label_config = movies_theme_get_scale_label_config();
$movie_categories = get_terms([
    'taxonomy' => 'category',
    'hide_empty' => true,
    'object_ids' => get_posts([
        'post_type' => 'movies',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'no_found_rows' => true,
    ]),
]);
$selected_category = isset($_GET['category']) ? sanitize_title(wp_unslash($_GET['category'])) : '';
$selected_category_exists = false;
$filters_state = isset($_GET['filters']) && sanitize_key(wp_unslash($_GET['filters'])) === 'closed'
    ? 'closed'
    : 'open';
$eyebrow_text = isset($_GET['eyebrow'])
    ? trim(wp_strip_all_tags(sanitize_text_field(wp_unslash($_GET['eyebrow']))))
    : 'I’m looking for';

if ($eyebrow_text === '') {
    $eyebrow_text = 'I’m looking for';
}

$movie_filter_keys = ['funny', 'scary', 'sadness', 'pacing'];
$selected_movie_filters = [];
$movie_meta_query = [];
$movie_tax_query = [];

foreach ($movie_categories as $movie_category) {
    if ($selected_category === $movie_category->slug) {
        $selected_category_exists = true;
        break;
    }
}

if (!$selected_category_exists) {
    $selected_category = '';
}

if ($selected_category !== '') {
    $movie_tax_query[] = [
        'taxonomy' => 'category',
        'field' => 'slug',
        'terms' => $selected_category,
    ];
}

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

$movies_query_args = [
    'post_type' => 'movies',
    'post_status' => 'publish',
    'posts_per_page' => 15,
    'orderby' => 'rand',
];

if ($movie_meta_query !== []) {
    $movies_query_args['posts_per_page'] = -1;
    $movies_query_args['orderby'] = 'title';
    $movies_query_args['order'] = 'ASC';
    $movies_query_args['meta_query'] = $movie_meta_query;
}

if ($movie_tax_query !== []) {
    $movies_query_args['posts_per_page'] = -1;
    $movies_query_args['orderby'] = 'title';
    $movies_query_args['order'] = 'ASC';
    $movies_query_args['tax_query'] = $movie_tax_query;
}

$movies_query = new WP_Query($movies_query_args);
?>

<main class="mx-auto max-w-[1000px] px-6 py-16 md:px-8 md:py-24">
  <?php get_template_part('header/site', 'header'); ?>

  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <article class="mt-[56px]">
        <header>
          <p class="theme-muted mb-3 text-base font-bold tracking-[0.04em] md:text-lg">
            <?php echo esc_html($eyebrow_text); ?>
          </p>
          <div class="relative flex">
            <h2 class="theme-strong shrink-0 text-3xl tracking-[-0.05em] md:text-5xl" data-filter-heading data-base-text="Movies that are...">
              <span class="opacity-90">Movies that are...</span>
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
          <div class="flex items-center justify-end gap-6">
            <button
              class="theme-strong theme-border cursor-pointer border bg-transparent px-4 py-2 text-sm font-bold tracking-[0.04em] transition-opacity hover:opacity-70"
              type="button"
              data-filter-toggle
              aria-expanded="<?php echo $filters_state === 'open' ? 'true' : 'false'; ?>"
            >
              <?php echo $filters_state === 'open' ? 'Hide Filters' : 'Show Filters'; ?>
            </button>
          </div>

          <form
            action="<?php echo esc_url(get_permalink()); ?>"
            class="grid gap-5 md:grid-cols-2 xl:grid-cols-5 xl:items-end <?php echo $filters_state === 'closed' ? 'hidden' : ''; ?>"
            method="get"
            data-filter-form
            data-filter-panel
          >
            <?php if (is_page() && !get_option('permalink_structure')) : ?>
              <input type="hidden" name="page_id" value="<?php echo esc_attr((string) get_the_ID()); ?>">
            <?php endif; ?>
            <input type="hidden" name="filters" value="<?php echo esc_attr($filters_state); ?>" data-filter-state>
            <label class="block md:col-span-2 xl:col-span-5">
              <span class="theme-muted mb-2 block text-xs font-bold tracking-[0.04em]">
                Genera
              </span>
              <span class="theme-border relative block border-b">
                <select
                  class="theme-body w-full appearance-none bg-transparent px-0 py-3 pr-8 text-lg font-bold focus:outline-none"
                  name="category"
                  data-filter-select
                >
                  <option value="">Select</option>
                  <?php foreach ($movie_categories as $movie_category) : ?>
                    <option value="<?php echo esc_attr($movie_category->slug); ?>" <?php selected($selected_category, $movie_category->slug); ?>>
                      <?php echo esc_html($movie_category->name); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <span class="theme-strong pointer-events-none absolute right-1 top-1/2 h-2.5 w-2.5 -translate-y-1/2 rotate-45 border-b-2 border-r-2" aria-hidden="true"></span>
              </span>
            </label>

            <?php foreach ($movie_filter_keys as $movie_filter_key) : ?>
              <label class="block">
                <span class="theme-muted mb-2 block text-xs font-bold tracking-[0.04em]">
                  <?php echo esc_html(ucfirst($movie_filter_key)); ?>
                </span>
                <span class="theme-border relative block border-b">
                  <select
                    class="theme-body w-full appearance-none bg-transparent px-0 py-3 pr-8 text-lg font-bold focus:outline-none"
                    name="<?php echo esc_attr($movie_filter_key); ?>"
                    data-filter-select
                  >
                    <option value="">Select</option>
                    <?php foreach ($scale_label_config[$movie_filter_key] as $scale_value => $scale_label) : ?>
                      <?php if ($scale_value === '0') : ?>
                        <?php continue; ?>
                      <?php endif; ?>
                      <option value="<?php echo esc_attr($scale_value); ?>" <?php selected($selected_movie_filters[$movie_filter_key], $scale_value); ?>>
                        <?php echo esc_html($scale_label); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <span class="theme-strong pointer-events-none absolute right-1 top-1/2 h-2.5 w-2.5 -translate-y-1/2 rotate-45 border-b-2 border-r-2" aria-hidden="true"></span>
                </span>
              </label>
            <?php endforeach; ?>

          </form>
        </section>

        <div data-filter-results>
          <?php if ($movies_query->have_posts()) : ?>
            <section class="mt-12 grid grid-cols-2 gap-x-6 gap-y-10 md:grid-cols-3 xl:grid-cols-5">
              <?php while ($movies_query->have_posts()) : ?>
              <?php
              $movies_query->the_post();
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

                    <h3 class="mt-4 flex items-center gap-2 text-xl tracking-[-0.04em] <?php echo $is_hidden_gem ? 'movie-title--hidden-gem' : 'theme-strong'; ?>">
                      <span><?php the_title(); ?></span>
                      <?php if ($gem_badge !== '') : ?>
                        <span class="movie-title__gem"><?php echo $gem_badge; ?></span>
                      <?php endif; ?>
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
        </div>
      </article>
    <?php endwhile; ?>
  <?php endif; ?>
</main>

<?php
get_footer();
?>
