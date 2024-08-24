<?php
// Not registered yet
if (empty(get_option('cs_product_validation_key', false))) {
  return;
}

// =============================================================================
// FUNCTIONS/GLOBAL/ADMIN/ADDONS/MARKUP/PAGE-HOME-BOX-EXTENSIONS.PHP
// -----------------------------------------------------------------------------
// Addons home page output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Page Output
// =============================================================================

// Page Output
// =============================================================================

$extensions = apply_filters("cs_max_get_plugins", []);
$iconDisabled = tco_common()->get_admin_icon( 'locked' );

$validation = cornerstone("Validation");

$status_icon_validated   = '<div class="tco-box-status tco-box-status-validated">' . tco_common()->get_admin_icon( 'unlocked' ) . '</div>';
$status_icon_unvalidated = '<div class="tco-box-status tco-box-status-unvalidated">' . tco_common()->get_admin_icon( 'locked' ) . '</div>';
$courses = cs_max_get_courses();
if (empty($courses['data'])) {
  $courses['data'] = [];
}

$app_url = apply_filters("cs_app_url", "");
$max_url = $app_url . "/max";

$max_plugin_external_ids = [];
?>

<!-- Max full container -->
<div class="tco-row tco-max-admin">
  <div class="tco-column" id="max">
    <div class="tco-box tco-box-extensions">

      <header class="tco-box-header">
        <h2 class="tco-box-title"><?php _e( 'Max', '__x__' ); ?></h2>
      </header>

        <div class="tco-box-content tco-pan">
          <p class="tco-extensions-info">
            <?php _e("Get instant access to premium <b>Courses</b>, <b>Expansion Packs</b>, and <b>Plugins</b>. New to Max?", "cornerstone"); ?>
            <a href="https://theme.co/max" target="_blank" rel="noopener">Learn More</a>.
            <?php _e("Recently purchased?", "cornerstone"); ?>
            <a id="tco-max-refresh" href="javascript:void(0)"><?php _e("Refresh Validation", "__x__"); ?></a>.
          </p>
        </div>

    <div class="tco-box-content tco-pan">

    <div class="tco-extensions">

<?php if ( !empty($extensions)) : ?>
  <?php
  // Max Plugins
  foreach ( $extensions as $extension) :
  $purchased = !empty($extension['purchased']);
  $icon = !$purchased
    ? $status_icon_unvalidated
    : $status_icon_validated;
  $max_plugin_external_ids[] = $extension['external_id'];
  ?>

            <div class="tco-extension tco-extension-<?php echo $extension['slug']; ?>
              tco-extension-<?php echo ( $extension['installed'] ) ? 'installed' : 'not-installed'; ?>"
              id="<?php echo $extension['slug']; ?>"
              data-tco-module="x-extension"
              style="position: relative"
            >

              <div class="tco-extension-content">
                <?php echo $icon; ?>
                <img class="tco-extension-img" src="<?php echo $extension['logo_url']; ?>" width="100" height="100">
                <h2 class="tco-extension-title"><?php echo __($extension['title'], "__x__"); ?></h2>
                <div class="tco-extension-info">

                  <a class="tco-extension-info-details" href="#" data-tco-toggle=".tco-extension-<?php echo $extension['slug']; ?> .tco-overlay"><?php _e( 'Details', '__x__' ); ?></a>
                </div>

                <?php if ($purchased) { ?>
                <a class="tco-btn" data-tco-module-target="manage"><?php _e( 'Install', '__x__' ); ?></a>
                <?php } else { ?>
                <a class="tco-btn tco-btn-yep" href="https://theme.co/checkout/<?php echo $extension['slug']; ?>" target="_blank">
                  <?php _e( 'Purchase', '__x__' ); ?>
                </a>
                <?php } ?>

              </div>

              <footer class="tco-extension-footer">
                <span class="tco-extension-status-icon"><?php tco_common()->admin_icon( 'yes' ); ?></span>
                <span class="tco-status-text"></span>
                <div class="tco-overlay">
                  <a class="tco-overlay-close" href="#" data-tco-toggle=".tco-extension-<?php echo $extension['slug']; ?> .tco-overlay"><?php tco_common()->admin_icon( 'no' ); ?></a>
                  <h4 class="tco-box-content-title"><?php echo $extension['title']; ?></h4>
                  <p><?php echo $extension['description_long']; ?></p>
                </div>
              </footer>

            </div>

  <?php endforeach; ?>

<?php endif; ?>


  <?php /* Courses */?>
  <?php foreach ($courses['data'] as $extension) :
  if (in_array($extension['external_id'], $max_plugin_external_ids)) {
    continue;
  }

  // Old API or data potentially
  $slug = empty($extension['slug'])
    ? strtolower(preg_replace("/\ /", "-", $extension['title']))
    : $extension['slug'];

  $purchased = !empty($extension['purchased']);
  $icon = !$purchased
    ? $status_icon_unvalidated
    : $status_icon_validated;

  $image = !empty($extension['tileImage'])
    ? $extension['tileImage']
    : $extension['image'];
  ?>

    <div class="tco-box tco-extension tco-extension-<?php echo $slug; ?>">

      <div class="tco-box-content tco-pan tco-ta-center">

            <div
              id="<?php echo "max-course-" . $slug; ?>"
            >

              <div class="tco-extension-content">
                <?php echo $icon; ?>

                <img class="tco-extension-img" src="<?php echo $image; ?>" style="width: auto; height: 100px;">
                <h2 class="tco-extension-title"><?php echo __($extension['title'], "__x__"); ?></h2>
                <div class="tco-extension-info">
                  <a class="tco-extension-info-details" href="#" data-tco-toggle=".tco-extension-<?php echo $slug; ?> .tco-overlay"><?php _e( 'Details', '__x__' ); ?></a>
                </div>
                <?php if ($purchased) { ?>
                <a class="tco-btn" href="<?php echo $max_url; ?>" target="_blank"><?php _e( 'Access', '__x__' ); ?></a>
                <?php } else { ?>
                <a class="tco-btn tco-btn-yep" href="https://theme.co/checkout/<?php echo $slug; ?>" target="_blank">
                  <?php _e( 'Purchase', '__x__' ); ?>
                </a>
                <?php } ?>
              </div>

              <footer class="tco-extension-footer">
                <span class="tco-extension-status-icon"><?php tco_common()->admin_icon( 'yes' ); ?></span>
                <span class="tco-status-text"></span>
                <div class="tco-overlay">
                  <a class="tco-overlay-close" href="#" data-tco-toggle=".tco-extension-<?php echo $slug; ?> .tco-overlay"><?php tco_common()->admin_icon( 'no' ); ?></a>
                  <h4 class="tco-box-content-title"><?php echo $extension['title']; ?></h4>
                  <p><?php echo @$extension['description']; ?></p>
                </div>
              </footer>

        </div>
      </div>
  </div>

  <?php endforeach; ?>

        </div>

        </div>
    </div>
  </div>
</div>
