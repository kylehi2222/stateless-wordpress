<div class="tco-reset tco-wrap tco-wrap-about tco-alt-cs">
  <div class="tco-content">
    <div class="wrap"></div>
    <div class="tco-main">

      <?php if ( ! $is_validated ) : ?>
      <div class="tco-row">
        <div class="tco-column">
        <?php do_action( '_cornerstone_home_not_validated' ); ?>
        </div>
      </div>
      <?php endif; ?>

      <div class="tco-row">
        <div class="tco-column">
          <?php
            include( cornerstone()->path . '/includes/views/admin/home-box-auto-updates.php' );
          ?>
        </div>
        <?php if ( ! apply_filters( '_cornerstone_integration_remove_support_box', false ) ) : ?>
        <div class="tco-column">
          <?php
            include( cornerstone()->path . '/includes/views/admin/home-box-support.php' );
          ?>
        </div>
      	<?php endif; ?>
      </div>

      <div class="tco-row">
        <div class="tco-column">
          <?php
            include( cornerstone()->path . '/includes/views/admin/home-box-templates.php' );
          ?>
        </div>
        <div class="tco-column tco-man"></div>
      </div>

      <?php do_action("cs_max_output_admin_extensions"); ?>

    </div>
  </div>

  <?php  include( cornerstone()->path . '/includes/views/admin/home-sidebar.php' ); ?>

</div>
