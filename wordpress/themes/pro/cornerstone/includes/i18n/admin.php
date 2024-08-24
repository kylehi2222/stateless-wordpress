<?php

/**
 * Localize strings for javascript
 */

$accept  = __( 'Yes, proceed', 'cornerstone' );
$decline = __( 'No, take me back', 'cornerstone' );

return array(

  'admin.editor-tab-logo-path'        => 'svg/logo-cornerstone', // Pro: 'svg/logo-flat-content'
  'admin.edit-with-cornerstone'       => __( 'Edit with Cornerstone', 'cornerstone' ),
  'admin.visual-tab'                  => __( 'Visual', 'cornerstone' ),
  'admin.text-tab'                    => __( 'Text', 'cornerstone' ),
  'admin.cornerstone-tab'             => __( 'Cornerstone', 'cornerstone' ),
  'admin.edit-with-wordpress'         => __( 'Edit with WordPress', 'cornerstone' ),
  'admin.insert-cornerstone'          => __( 'Insert Shortcode', 'cornerstone' ),
  'admin.updating'                    => __( 'Updating', 'cornerstone' ),
  'admin.confirm-yep'                 => __( 'Yep', 'cornerstone' ),
  'admin.confirm-nope'                => __( 'Nope', 'cornerstone' ),
  'admin.manual-edit-warning'         => __( 'Hold up! You&apos;re welcome to make changes to the content. However, these will not be reflected in Cornerstone. If you edit the page in Cornerstone again, any changes made here will be overwritten. Do you wish to continue?', 'cornerstone' ),
  'admin.overwrite-warning'           => __( 'Hold up! The content has been modified outside of Cornerstone. Editing in Cornerstone will replace the current content. Do you wish to continue?', 'cornerstone' ),
  'admin.post-does-not-exist-warning' => __( 'Please save this content at least once before editing.', 'cornerstone' ),
  'admin.post-editor-back'            => __( 'Go Back', 'cornerstone' ),
  'admin.manual-edit-accept'          => $accept,
  'admin.manual-edit-decline'         => $decline,
  'admin.overwrite-accept'            => $accept,
  'admin.overwrite-decline'           => $decline,
  'admin.default-title'               => __( 'Default', 'cornerstone'),

  'admin.dashboard-title'                  => __( 'Cornerstone', 'cornerstone'),
  'admin.dashboard-menu-title'             => __( 'Home', 'cornerstone'),
  'admin.dashboard-status-title'           => __( 'Status', 'cornerstone' ),
  'admin.dashboard-settings-title'         => __( 'Settings', 'cornerstone' ),
  'admin.dashboard-settings-path'          => 'cornerstone-settings',
  'admin.dashboard-settings-update'        => __( 'Update', 'cornerstone' ),
  'admin.dashboard-settings-save-title'    => __( 'Save Settings', 'cornerstone' ),
  'admin.dashboard-settings-save-update'   => __( 'Update', 'cornerstone' ),
  'admin.dashboard-settings-save-info'     => __( 'Once you are satisfied with your settings, click the button below to save them.', 'cornerstone' ),
  'admin.dashboard-settings-save-updating' => __( 'Updating...', 'cornerstone' ),
  'admin.dashboard-settings-save-updated'  => __( 'Settings Saved!', 'cornerstone' ),
  'admin.dashboard-settings-save-error'    => __( 'Sorry! Unable to Save', 'cornerstone' ),

  'admin.dashboard-location-title'         => __( 'Location', 'cornerstone' ),

  'admin.dashboard-settings-system-title' => __( 'System', 'cornerstone' ),

  'admin.permissions.title'            => __( 'Permissions', 'cornerstone' ),
  'admin.permissions.config'           => __( 'Configure', 'cornerstone' ),
  'admin.permissions.close'            => __( 'Close', 'cornerstone' ),
  'admin.permissions.enabled'          => __( 'Enabled', 'cornerstone' ),
  'admin.permissions.toggle-all'       => __( 'Toggle All Permissions', 'cornerstone' ),
  'admin.permissions.insert'           => __( 'Insert', 'cornerstone' ),
  'admin.permissions.import-terms'     => __( 'Import Terms', 'cornerstone' ),

  'admin.permissions.templates'        => __( 'Templates', 'cornerstone' ),
  'admin.permissions.element-library'  => __( 'Elements', 'cornerstone' ),
  'admin.permissions.global'           => __( 'Global', 'cornerstone' ),
  'admin.permissions.colors'           => __( 'Colors', 'cornerstone' ),
  'admin.permissions.fonts'            => __( 'Fonts', 'cornerstone' ),
  'admin.permissions.theme-options'    => __( 'Theme Options', 'cornerstone' ),
  'admin.permissions.theme-options-import' => __( 'Theme Options Import', 'cornerstone' ),
  'admin.permissions.theme-options-export' => __( 'Theme Options Export', 'cornerstone' ),
  'admin.permissions.export-documents' => __( 'Export Documents (Alpha)', 'cornerstone' ),
  'admin.permissions.classic-elements' => __( 'All Classic Elements', 'cornerstone' ),

  'admin.permissions.create'           => __( 'Create', 'cornerstone' ),
  'admin.permissions.delete'           => __( 'Delete', 'cornerstone' ),
  'admin.permissions.element-locking'  => __( 'Lock / Unlock Elements', 'cornerstone' ),
  'admin.permissions.edit-custom-css'  => __( 'Edit Custom CSS', 'cornerstone' ),
  'admin.permissions.edit-custom-js'   => __( 'Edit Custom JS', 'cornerstone' ),

  'admin.permissions.save'             => __( 'Create', 'cornerstone' ),
  'admin.permissions.manage-library'   => __( 'Manage Library', 'cornerstone' ),
  'admin.permissions.themeco-templates'   => __( 'Themeco Templates', 'cornerstone' ),
  'admin.permissions.max'   => __( 'Max', 'cornerstone' ),

  'admin.permissions.update' => __('Update', 'cornerstone'),

  // Element library
  'admin.permissions.inspect' => __('Inspect', 'cornerstone'),
  'admin.permissions.copy' => __('Copy', 'cornerstone'),
  'admin.permissions.paste' => __('Paste', 'cornerstone'),
  'admin.permissions.paste-style' => __('Paste Style', 'cornerstone'),
  'admin.permissions.clear-style' => __('Clear Style', 'cornerstone'),
  'admin.permissions.apply-preset' => __('Apply Preset', 'cornerstone'),
  'admin.permissions.manage' => __('Manage', 'cornerstone'),
  'admin.permissions.save-preset' => __('Save Preset', 'cornerstone'),
  'admin.permissions.show-in-library' => __('Show In Library', 'cornerstone'),




  'admin.status.system.heading'      => __( 'System', 'cornerstone' ),
  'admin.status.system.sub-heading'  => __( 'Clear System Cache', 'cornerstone' ),
  'admin.status.system.paragraph' => __(
    'For slower page loads Elements will remember the CSS generated when they were last saved. This is automatically cleared when Cornerstone is updated. It may be useful to clear manually if any Elements are missing styling',
    'cornerstone'
  ),

  'admin.status.system.button-default'      => __( 'Clear System Cache', 'cornerstone' ),
  'admin.status.system.button-processing'   => __( 'Clearing...', 'cornerstone' ),
  'admin.status.system.button-success'      => __( 'Cleared!', 'cornerstone' ),
  'admin.status.system.button-error'        => __( 'Clear System Cache Error', 'cornerstone' ),

  'admin.status.extensions.heading'      => __( 'Advanced', 'cornerstone' ),

  'admin.extensions.external-api.title'      => __( 'External API', 'cornerstone' ),
  'admin.extensions.external-api.allow-list' => __( 'Allow List', 'cornerstone' ),
  'admin.extensions.external-api.allow-list-description' => __( 'Enter all external domains that you would like to allow your website to access. Leave this empty to allow any domain. Each new domain should be entered onto a new line. Include the protocol (e.g. http or https)', 'cornerstone' ),
  'admin.extensions.external-api.description' => __( 'Easily connect to a number of different External Endpoints. When enabled extra Looper Providers, Theme Options, and Dynamic Content features will be added to Cornerstone', 'cornerstone' ),

  'admin.extensions.beta'      => __( 'Beta', 'cornerstone' ),

  'admin.docs.title'      => __( 'Docs', 'cornerstone' ),




  // Standalone
  // ----------

  'admin.plugin-update-nothing'   => __( 'Nothing to report.', 'cornerstone' ),
  'admin.plugin-update-new'       => __( 'New version available!', 'cornerstone' ),
  'admin.plugin-update-error'     => __( 'Unable to check for updates. Try again later.', 'cornerstone' ),
  'admin.plugin-update-checking'  => __( 'Checking&hellip;', 'cornerstone' ),
  'admin.plugin-update-changelog' => __( 'Visit the <a href="http://theme.co/changelog/#cornerstone">Themeco Changelog</a> for more information.', 'cornerstone' ),
  'admin.plugin-update-notice'    => __( '<a href="%s">Validate to enable automatic updates</a>', 'cornerstone' ),

  'admin.validation-global-notice'   => __( 'This Cornerstone license is ​<strong>not validated</strong>​. <a href="%s">Fix</a>.', 'cornerstone' ),
  'admin.validation-verifying'       => __( 'Verifying license&hellip;', 'cornerstone' ),
  'admin.validation-couldnt-verify'  => __( '<strong>Uh oh</strong>, we couldn&apos;t check if this license was valid. <a data-tco-error-details href="#">Details.</a>', 'cornerstone' ),
  'admin.validation-congrats'        => __( '<strong>Congratulations!</strong> Cornerstone is now validated for this site!', 'cornerstone ' ),
  'admin.validation-go-back'         => __( 'Go Back', 'cornerstone' ),
  'admin.validation-login'           => __( 'Login or Register', 'cornerstone' ),
  'admin.validation-manage-licenses' => __( 'Manage Licenses', 'cornerstone'),
  'admin.validation-revoke-confirm'  => __( 'By revoking validation, you will no longer receive automatic updates. The site will still be linked in your Themeco account, so you can re-validate at anytime.<br/><br/> Visit "Licenses" in your Themeco account to transfer a license to another site.', 'cornerstone' ),
  'admin.validation-revoke-accept'   => __( 'Yes, revoke validation', 'cornerstone' ),
  'admin.validation-revoke-decline'  => __( 'Stay validated', 'cornerstone' ),
  'admin.validation-revoking'        => __( 'Revoking&hellip;', 'cornerstone' ),
  'admin.validation-revoked'         => __( '<strong>Validation revoked.</strong> You can re-assign licenses from <a href="%s" target="_blank">Manage Licenses</a>.', 'cornerstone' ),
  'admin.validation-msg-invalid'     => __( 'We&apos;ve checked the code, but it <strong>doesn&apos;t appear to be an Cornerstone purchase code or Themeco license.</strong> Please double check the code and try again.', 'cornerstone' ),
  'admin.validation-msg-new-code'    => __( 'This looks like a <strong>brand new purchase code that hasn&apos;t been added to a Themeco account yet.</strong> Login to your existing account or register a new one to continue.', 'cornerstone' ),
  'admin.validation-msg-cant-link'   => __( 'Your code is valid, but <strong>we couldn&apos;t automatically link it to your site.</strong> You can add this site from within your Themeco account.', 'cornerstone' ),
  'admin.validation-msg-in-use'      => __( 'Your code is valid but looks like it has <strong>already been used on another site.</strong> You can revoke and re-assign within your Themeco account.', 'cornerstone' ),

  'admin.tco-connection-error' => __( 'Could not establish connection. For assistance, please start by reviewing our article on troubleshooting <a href="https://theme.co/docs/problems-with-product-validation/">connection issues.</a>', 'cornerstone' ),

);
