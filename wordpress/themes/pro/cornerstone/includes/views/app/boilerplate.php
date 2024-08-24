
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="tco-ui-theme-<?php echo $data['theme'];?>">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $data['title']; ?></title>
<?php
  wp_enqueue_scripts();
  wp_print_styles();
  wp_print_head_scripts();
?>

<?php if (!empty($data['favicon'])) { ?>
  <link rel="icon" type="image/x-icon" href="<?php echo $data['favicon']; ?>">
<?php } ?>

</head>
<body <?php echo $data['body_classes']; ?>>
  <?php
  // <div class="tco-root-preloader tco-progress-overlay">
  //     <div class="tco-progress-overlay-content">
  //     <svg class="tco-svg is-logo-cornerstone" width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><g><path d="M0.9,1.9l14-1.7c0.5-0.1,1,0.3,1.1,0.9c0,0,0,0.1,0,0.1v13.7c0,0.6-0.4,1-1,1c0,0-0.1,0-0.1,0l-14-1.6c-0.5-0.1-0.9-0.5-0.9-1V2.9C0,2.4,0.4,1.9,0.9,1.9z M7.5,9.5c-0.4,0.4-0.9,0.6-1.4,0.6c-1.4,0-1.9-1-1.9-1.9c0-0.9,0.6-1.9,1.9-1.9c0.5,0,1,0.2,1.4,0.5l0.7-0.7C7.6,5.5,6.9,5.2,6.1,5.2c-2,0-3,1.5-3,3c0,1.5,0.9,2.9,3,2.9c0.8,0,1.6-0.3,2.1-0.8L7.5,9.5z M12.9,6.2c-0.4-0.9-1.3-1.1-2.2-1.1c-1,0-2.2,0.5-2.2,1.6c0,1.3,1.1,1.6,2.2,1.7C11.5,8.5,12,8.8,12,9.3c0,0.6-0.6,0.8-1.3,0.8c-0.7,0-1.3-0.3-1.6-0.9L8.3,9.7c0.4,1,1.3,1.4,2.4,1.4c1.2,0,2.4-0.5,2.4-1.8c0-1.3-1.1-1.6-2.3-1.8c-0.7-0.1-1.2-0.2-1.2-0.7c0-0.4,0.4-0.7,1.2-0.7c0.6,0,1.1,0.3,1.3,0.6L12.9,6.2z"></path></g></svg>
  //     </div>
  //   </div>
  ?>
  <?php
    wp_print_footer_scripts();
    wp_admin_bar_render();
    wp_auth_check_html();

    if ( function_exists( 'wp_underscore_playlist_templates' ) && function_exists( 'wp_print_media_templates' ) ) {
      wp_underscore_playlist_templates();
      wp_print_media_templates();
    }
  ?>
</body>
</html>
