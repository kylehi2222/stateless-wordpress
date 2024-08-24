<?php

// =============================================================================
// VIEWS/RENEW/_CONTENT-POST-FOOTER.PHP
// -----------------------------------------------------------------------------
// Standard <footer> output for various posts.
// =============================================================================

?>

<?php if ( has_tag() ) : ?>
  <footer class="entry-footer cf">
    <?php echo get_the_tag_list( '<p>' . x_icon_get("f02c", "x-icon-tags") . __( 'Tags:', '__x__'), ', ', '</p>' ); ?>
  </footer>
<?php endif; ?>
