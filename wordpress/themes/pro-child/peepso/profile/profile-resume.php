<?php
$PeepSoProfile = PeepSoProfile::get_instance();
$small_thumbnail = PeepSo::get_option('small_url_preview_thumbnail', 0);
$view_user_id = PeepSoProfileShortcode::get_instance()->get_view_user_id();

$parameters = wp_json_encode([
    'user_id' => $view_user_id
]);
?>
<div class="peepso">
  <div class="ps-page ps-page--profile">
    <?php PeepSoTemplate::exec_template('general', 'navbar'); ?>
    <div id="ps-profile" class="ps-profile">
      <?php PeepSoTemplate::exec_template('profile', 'focus', ['current' => 'resume']); ?>
      <?php echo do_shortcode("[cs_component id='sbDe6Nn6ZhyfXe5THT' parameters='" . $parameters . "']"); ?>
    </div>
  </div>
</div>
