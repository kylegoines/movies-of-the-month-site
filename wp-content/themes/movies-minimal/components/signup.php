<?php
$signup_status = isset($_GET['signup_status'])
    ? sanitize_key(wp_unslash($_GET['signup_status']))
    : '';
$eyebrow_text = $args['eyebrow_text'] ?? 'Send a note';
$label_text = $args['label_text'] ?? 'Get In Touch';
$heading_text = $args['heading_text'] ?? 'Drop a message right here.';
$preset_topic = $args['preset_topic'] ?? '';
$show_label_button = $args['show_label_button'] ?? true;
$variant = $args['variant'] ?? 'default';
$section_classes = 'home-signup';

if ($variant !== '') {
    $section_classes .= ' home-signup--' . sanitize_html_class($variant);
}

?>

<section class="<?php echo esc_attr($section_classes); ?>" id="home-signup">
    <!-- <div class="bg-red-50"> -->
  
  <div class="home-signup__inner ">
    <div class="max-w-[1000px] space-y-6 mx-auto relative">
        <?php if ($show_label_button) : ?>
    <button class="home-signup__label" type="button" data-signup-jump>
      <span class="home-signup__label-text text-[32px] leading-none font-bold"><?php echo esc_html($label_text); ?></span>
    </button>
  <?php endif; ?>
      <div class="space-y-3">
        <h2 class="text-3xl leading-none font-bold md:text-4xl"><?php echo esc_html($heading_text); ?></h2>
      </div>

      <?php if ($signup_status === 'success') : ?>
        <p class="border border-[var(--signup-border)] px-4 py-3 text-sm font-bold">Thanks. Your message was sent.</p>
      <?php elseif ($signup_status === 'error') : ?>
        <p class="border border-[var(--signup-border)] px-4 py-3 text-sm font-bold">Something went wrong. Please try again.</p>
      <?php endif; ?>

      <?php if ($signup_status !== 'success') : ?>
        <form class="space-y-5" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
          <input type="hidden" name="action" value="movies_theme_contribution_form">
          <input type="hidden" name="redirect_to" value="<?php echo esc_url(home_url('/#home-signup')); ?>">
          <?php wp_nonce_field('movies_theme_contribution_form', 'movies_theme_contribution_nonce'); ?>

          <div class="hidden" aria-hidden="true">
            <label for="contribution-company">Company</label>
            <input id="contribution-company" type="text" name="company" tabindex="-1" autocomplete="off">
          </div>

          <label class="block space-y-2">
            <span class="block text-sm font-bold uppercase tracking-[0.08em]">Name</span>
            <input
              class="w-full border border-[var(--signup-border)] bg-transparent px-4 py-3 text-base font-bold text-[var(--signup-foreground)] placeholder:text-[var(--signup-muted)] focus:outline-none"
              type="text"
              name="contribution_name"
              placeholder="Your name"
            >
          </label>

          <label class="block space-y-2">
            <span class="block text-sm font-bold uppercase tracking-[0.08em]">Subject</span>
            <span class="block border border-[var(--signup-border)]">
              <select
                class="w-full appearance-none bg-transparent px-4 py-3 text-lg font-bold text-[var(--signup-foreground)] focus:outline-none"
                style="color-scheme: light;"
                name="contribution_topic"
                required
              >
                <option value="">Select a subject</option>
                <option value="write_for_site" <?php selected($preset_topic, 'write_for_site'); ?>>I want to be a contributor</option>
                <option value="movie_recommendation" <?php selected($preset_topic, 'movie_recommendation'); ?>>Movie Recommendation</option>
                <option value="movie_spotlight_request" <?php selected($preset_topic, 'movie_spotlight_request'); ?>>Movie Spotlight Request</option>
                <option value="general_inquiry" <?php selected($preset_topic, 'general_inquiry'); ?>>General Inquiry</option>
              </select>
            </span>
          </label>

          <label class="block space-y-2">
            <span class="block text-sm font-bold uppercase tracking-[0.08em]">Thoughts?</span>
            <textarea
              class="min-h-[180px] w-full border border-[var(--signup-border)] bg-transparent px-4 py-3 text-base leading-7 text-[var(--signup-foreground)] placeholder:text-[var(--signup-muted)] focus:outline-none"
              name="contribution_message"
              placeholder="Write your message here."
              required
            ></textarea>
          </label>

          <button
            class="inline-flex cursor-pointer items-center border border-[var(--signup-foreground)] px-5 py-3 text-base font-bold transition-opacity hover:opacity-70"
            type="submit"
          >
            Send Message
          </button>
        </form>
      <?php endif; ?>
    </div>
  </div>
      <!-- </div> -->
</section>
