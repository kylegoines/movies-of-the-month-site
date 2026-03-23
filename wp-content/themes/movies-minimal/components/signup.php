<?php
$current_view = $args['current_view'] ?? '';

if ($current_view !== '' || !shortcode_exists('aetta_email_capture')) {
    return;
}
?>

<section class="home-signup" id="home-signup">
  <button class="home-signup__label" type="button" data-signup-jump>
    <span class="home-signup__label-text text-[32px] leading-none font-bold">Want to Contribute?</span>
  </button>
  <div class="home-signup__inner">
    <?php echo do_shortcode('[aetta_email_capture]'); ?>
  </div>
</section>
