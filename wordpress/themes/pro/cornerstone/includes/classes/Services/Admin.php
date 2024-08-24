<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Util\AdminAjax;
use Themeco\Cornerstone\Util\MinifyCss;
use Themeco\Cornerstone\Util\JsAsset;
use Themeco\Cornerstone\Util\View;
use Themeco\Cornerstone\Plugin;

class Admin implements Service {

  protected $plugin;

  protected $http;
  protected $permalinks;
  protected $permissions;
  protected $assignments;
  protected $settings;
  protected $config;
  protected $i18n;
  protected $view;
  protected $minifyCss;
  protected $ajaxOverrideNotice;
  protected $ajaxDismiss;
  protected $jsAsset;

  public function __construct(Plugin $plugin, Http $http, Permalinks $permalinks, Permissions $permissions, Settings $settings, Config $config, View $view, I18n $i18n, Assignments $assignments, MinifyCss $minifyCss, AdminAjax $ajaxOverrideNotice, AdminAjax $ajaxDismiss, JsAsset $jsAsset) {
    $this->plugin = $plugin;
    $this->http = $http;
    $this->permalinks = $permalinks;
    $this->permissions = $permissions;
    $this->assignments = $assignments;
    $this->settings = $settings;
    $this->config = $config;
    $this->i18n = $i18n;
    $this->view = $view;
    $this->minifyCss = $minifyCss;
    $this->ajaxOverrideNotice = $ajaxOverrideNotice;
    $this->ajaxDismiss = $ajaxDismiss;
    $this->jsAsset = $jsAsset;
  }

  public function setup() {
    add_action('init', [ $this, 'init' ], -1000);
  }

  public function init() {

    if ( ! is_user_logged_in() ) return;

    $this->ajaxOverrideNotice->setAction( 'override' )
			->setHandler( [ $this, 'ajax_override'] )
			->start();

    $this->ajaxDismiss->setAction( 'dismiss_validation_notice' )
			->setHandler( [ $this, 'ajax_dismiss_validation_notice'] )
			->start();

    add_action( 'cs_late_template_redirect', [ $this, 'populate_admin_toolbar' ], 100 );
    add_action( 'admin_bar_menu', array( $this, 'addToolbarLinks' ), 999 );
    add_action( 'admin_bar_init', array( $this, 'admin_bar_css' ) );

    if ( ! is_admin() ) {
      return;
    }

    add_action( 'admin_menu',            array( $this, 'dashboard_menu' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
    add_filter( 'page_row_actions',      array( $this, 'addRowActions' ), 10, 2 );
    add_filter( 'post_row_actions',      array( $this, 'addRowActions' ), 10, 2 );
    add_action( 'admin_notices',         array( $this, 'notices' ), 20 );
    add_action( 'admin_print_scripts',   array( $this, 'admin_print_scripts' ), 9999);

  }
  /**
   * Store script data potentially used by multiple modules
   * @var array
   */
  public $script_data = array();


  protected $front_end_toolbar_items = [];


  public function ajax_override() {

    if ( isset( $_POST['post_id'] ) && current_user_can( $this->permissions->getPostCapability( $_POST['post_id'], 'edit_post' ), $_POST['post_id'] ) ) {
      update_post_meta( $_POST['post_id'], '_cornerstone_override', true );
    }

    return wp_send_json_success();

  }

  public function ajax_dismiss_validation_notice() {
    update_option( 'cornerstone_dismiss_validation_notice', true );
    return wp_send_json_success();
  }

  public function add_script_data( $handle, $callback ) {
    $this->script_data[$handle] = $callback;
  }

  public function get_script_data() {

    $modules = array();

    foreach ($this->script_data as $handle => $callback ) {
      if ( is_callable( $callback ) ) {
        $modules[$handle] = call_user_func( $callback );
      }
    }

    $notices = array();
    if ( isset( $_REQUEST['notice'] ) ) {
      $notices = explode( '|', sanitize_text_field( $_REQUEST['notice'] ) );
    }

    return array(
      'modules' => $modules,
      'notices' => $notices
    );

  }

  /**
   * Enqueue Admin Scripts and Styles
   */
  public function enqueue( $hook ) {

    $admin_js_config = apply_filters( 'cs_admin_app_data', $this->detect_admin_js( $hook ), $hook );




    if ( ! empty( $admin_js_config ) ) {
      ;
      $admin_script_asset = $this->jsAsset->get( "assets/js/admin" );
      wp_register_script( 'cs-admin-js', $admin_script_asset['url'], array_unique( $admin_js_config['deps'] ), $admin_script_asset['version'], true );
      wp_localize_script( 'cs-admin-js', 'csAdminData', $admin_js_config['data'] );
      wp_enqueue_script( 'cs-admin-js' );

      if (isset($admin_js_config['data']['post-editor'])) {
        wp_register_style( 'cs-post-editor', false );
        wp_add_inline_style( 'cs-post-editor', $this->minifyCss->run( $this->get_post_editor_css() ) );
        wp_enqueue_style( 'cs-post-editor' );
      }
    }

    wp_add_inline_style( 'admin-menu', $this->minifyCss->run( $this->get_admin_menu_css() ) );
    wp_add_inline_style( 'admin-menu', $this->minifyCss->run( $this->get_admin_miscellaneous_css() ) );

  }

  public function detect_admin_js( $hook ) {


    $post = $this->permissions->contextualPost();
    $post_id = ( $post ) ? $post->ID : 'new';

    $data = [
      'tco' => [
        'strings' => $this->i18n->group( 'admin', false ),
        'logo' => tco_common()->get_themeco_svg()
      ],
      'common' => [
        'ajax_url'  => $this->http->ajaxUrl(),
        'homeURL'   => preg_replace('/\?lang=.*/' , '', home_url()),
        'post_id'   => $post_id,
        '_cs_nonce' => $this->http->createNonce(),
        'strings'   => $this->i18n->group( 'admin', false ),
      ]
    ];

    $deps = [ 'wp-util' ];

    if ( false !== strpos( $hook, 'cornerstone-home' ) ) {
      $deps = array_merge( $deps, [ 'react', 'react-dom' ] );
      $data['home'] = $this->get_script_data();
    }


    if ( false !== strpos( $hook, 'cornerstone-settings' ) ) {

      $deps = array_merge( $deps, [ 'wp-api-fetch', 'jquery', 'lodash', 'moment', 'react', 'react-dom' ] );
      $data['settings'] = [
        'data' => $this->settings->getAll(),
        'controls' => $this->settings->controls(),
        'role-manager' => $this->permissions->getAppData(),
        'extensions-manager' => apply_filters("cs_extension_settings", []),
      ];

    }

    // Start post editor integration script
    if ( get_option("cs_post_editor_edit_with_cornerstone", true) && $this->isPostEditor( $hook ) ) {

      if ( $post ) {
        $skip = array();
        $skip[] = (int) get_option( 'page_for_posts' );

        if ( function_exists('wc_get_page_id') ) {
          $skip[] = (int) wc_get_page_id( 'shop' );
        }

        if ( ! in_array( (int) $post->ID, $skip, true ) ) {
          $data['post-editor'] = [
            'editUrl' => $this->get_app_route_url( 'edit', [ 'id' => '{{post_id}}' ]),
            'usesCornerstone' => ( cs_uses_cornerstone( $post ) ) ? 'true' : 'false',
            'editorTabMarkup' => $this->view->name('admin/editor-tab')->render( false ),
            'editorTabContentMarkup' => $this->view->name('admin/editor-tab-content')->render( false ),
          ];
        }

      }

    }

    return [ 'data' => $data, 'deps' => $deps ];
  }


  /**
   * Determine if the post editor is being viewed, and Cornerstone is available
   * @param  string  $hook passed through from admin_enqueue_scripts hook
   * @return boolean
   */
  public function isPostEditor( $hook ) {

    if ( 'post.php' === $hook && isset( $_GET['action'] ) && 'edit' === $_GET['action'] ) {
      return $this->permissions->userCanAccessPostType();
    }


    if ( 'post-new.php' === $hook && isset( $_GET['post_type'] ) ) {
      return in_array( $_GET['post_type'], $this->permissions->getUserPostTypes(), true );
    }

    if ( 'post-new.php' === $hook && ! isset( $_GET['post_type'] ) ) {
      return in_array( 'post', $this->permissions->getUserPostTypes(), true );
    }

    return false;
  }

  public function get_post_editor_css() {

    ob_start(); ?>

    .cornerstone-active .switch-cornerstone {
      background-color: white;
      color: #555;
      border-bottom-color: white;
    }

    .cs-editor-container {
      margin: 0;
      border: 15px solid #fff;
      padding: 14vmin 1rem;
      text-align: center;
      background-color: #f1f1f1;
    }

    .cs-editor-container-logo {
      width: 120px;
      margin: 0 auto 15px;
    }


    .cs-disable-gutenburg #editor .edit-post-visual-editor .editor-block-list__layout,
    .cs-disable-gutenburg #editor .edit-post-text-editor__body .editor-post-text-editor,
    .cs-disable-gutenburg #editor .edit-post-header-toolbar,
    .cs-disable-gutenburg #editor .edit-post-text-editor__toolbar,
    .cs-disable-gutenburg #editor .interface-interface-skeleton__body .editor-styles-wrapper {
      display: none!important;
    }
    <?php return ob_get_clean();
  }

  /**
   * Register the Dashboard Menu items
   */
  public function dashboard_menu() {

    $title = csi18n('admin.dashboard-title');
    $is_standalone = apply_filters( 'cornerstone_dashboard_home', true );
    $menu_page_slug = $is_standalone ? 'cornerstone-home' : 'cornerstone-launch-editor';

    // Sidebar menu page
    add_menu_page(
      $title,
      $title,
      'manage_options',
      $menu_page_slug,
      $is_standalone ? [ $this, 'render_home_page' ] : [ $this, 'render_editor_redirect' ],
      $this->make_menu_icon(),
      4
    );

    if ($is_standalone) {
      add_submenu_page( 'cornerstone-home', $title, csi18n('admin.dashboard-menu-title'), 'manage_options', 'cornerstone-home', array( $this, 'render_home_page' ) );
    }

    $custom_items = $this->get_custom_menu_items('dashboard');

    $settings_path = csi18n('admin.dashboard-settings-path');
    $settings_title = csi18n('admin.dashboard-settings-title');
    $divider = ( count($custom_items) > 1 ) ? 'data-tco-admin-menu-divider' : '';
    $settings_title = "<span $divider>$settings_title</span>";

    // Settings sub menu button
    add_submenu_page(
      $menu_page_slug,
      $title,
      csi18n('app.theme-options.title'),
      'manage_options',
      'cornerstone-theme-options',
      [ $this, 'render_editor_theme_options' ]
    );

    // Templates sidebar
    if ( $this->permissions->userCan('template.manage_library') ) {
      add_submenu_page(
        $menu_page_slug,
        $title,
        csi18n( "common.title.template-manager" ),
        'manage_options',
        'cornerstone-design-cloud',
        [ $this, 'render_editor_design_cloud' ]
      );
    }

    // Settings sub menu button
    add_submenu_page(
      $menu_page_slug,
      $title,
      $settings_title,
      'manage_options',
      'cornerstone-settings',
      array( $this, 'render_settings_page' )
    );

    global $submenu;

    $previous = null;
    $prev_index = 0;
    $permitted_items = array();


    foreach ($custom_items as $key => $value) {

      $item = array(
        'parent' => $menu_page_slug,
        'divider' => '',
        'class' => $key,
        'title' => $value['title'],
        'capability' => isset( $value['capability'] ) ? $value['capability'] : 'read',
        'url' => $value['url'],
      );

      if ( ! is_null($previous) && $custom_items[$previous]['group'] !== $value['group'] ) {
        $permitted_items[$prev_index]['divider'] = 'data-tco-admin-menu-divider';
      }

      $permitted_items[] = $item;

      $previous = $key;
      $prev_index = count($permitted_items) - 1;

    }

    $end = count($permitted_items) - 1;
    if ( isset( $permitted_items[$end] ) ) {
      $permitted_items[$end]['divider'] = 'data-tco-admin-menu-divider';
    }

    foreach ($permitted_items as $item) {
      $title = '<span ' . $item['divider'] . ' class="' . $item['class'] .'">' . $item['title'] .'</span>';
      $submenu[$item['parent']][] = array( $title, $item['capability'], $item['url'] );
    }

  }


  public function get_custom_menu_items( $context ) {

    $items = array();

    return $items;
  }

  public function render_home_page() {

    if ( ! has_action( '_cornerstone_home_not_validated' ) ) {
      add_action( '_cornerstone_home_not_validated', array( $this, 'render_not_validated' ) );
    }

    do_action( '_cornerstone_home_before' );

    $is_validated             = $this->is_validated();
    $status_icon_dynamic      = ( $is_validated ) ? '<div class="tco-box-status tco-box-status-validated">' . tco_common()->get_admin_icon( 'unlocked' ) . '</div>' : '<div class="tco-box-status tco-box-status-unvalidated">' . tco_common()->get_admin_icon( 'locked' ) . '</div>';

    include( $this->plugin->path . '/includes/views/admin/home.php' );

    do_action( '_cornerstone_home_after' );

  }

  public function is_validated() {
    return ( get_option( 'cs_product_validation_key', false ) !== false );
  }

  public function render_not_validated() {
    $this->view->name( 'admin/home-validation' )->render();
  }

  /**
   * Callback to render the settings page.
   */
  public function render_settings_page() {
    echo cs_tag('div', [ 'class' => 'tco-reset tco-wrap tco-wrap-settings tco-alt-cs', 'data-tco-admin-app' => 'settings' ], '' );
  }

  /**
   * Needed as a window location because we will end up
   * dealing with modifying header issues based on plugins
   * and all that
   */
  public function render_editor_redirect($extra = '') {
    //Launch editor hook
    $url = $this->get_app_route_url($extra);
    echo "<script>window.location = '{$url}'</script>";
  }

  /**
   * Theme options redirect
   */
  public function render_editor_theme_options() {
    $this->render_editor_redirect('theme-options');
  }

  /**
   * Theme options redirect
   */
  public function render_editor_design_cloud() {
    $this->render_editor_redirect('design-cloud');
  }

  /**
   * Add "Edit With Cornerstone" links to the WP List tables
   * Filter applied to page_row_actions and post_row_actions
   * @param array $actions
   * @param object $post
   */
  public function addRowActions( $actions, $post ) {

    $skip = array();
    $skip[] = (int) get_option( 'page_for_posts' );

    if ( function_exists('wc_get_page_id') ) {
      $skip[] = (int) wc_get_page_id( 'shop' );
    }


    if ( ! in_array( (int) $post->ID, $skip, true ) && $this->permissions->userCanAccessPostType( $post ) ) {
      // $url = $this->get_edit_url( $post );
      $url = preg_replace('/\?lang=.*\/\#/' , '#', $this->get_edit_url( $post ));
      $label = csi18n('admin.edit-with-cornerstone');
      $actions['edit_cornerstone'] = "<a href=\"$url\">$label</a>";
    }

    return $actions;
  }


  public function populate_admin_toolbar() {

    if ( ! is_user_logged_in() ) {
      return;
    }

    $items = array();

    /**
     * Add "Edit with Cornerstone" button on the toolbar
     * This is only added on singlular views, and if the post type is supported
     */

    if ( is_singular() ) {

      $post = $this->permissions->contextualPost();
      $canEditPost = $this->permissions->userCanAccessPostType( $post->post_type );

      if (
        $post
        && $canEditPost
        && cs_uses_cornerstone( $post )
      ) {
        $post_type_obj = get_post_type_object( $post->post_type );

        $items['tco-edit-link'] = array(
          'title' => sprintf( csi18n('common.toolbar-edit-link'), $post_type_obj->labels->singular_name ),
          'url' => preg_replace('/\?lang=.*\/\#/' , '#', $this->get_edit_url( $post )),
          'group' => 'contextual'
        );

      }
    }

    //Add tool bar edit actions
    if ( $this->permissions->userCan('layout') ) {

      $layout = $this->assignments->get_last_active_layout();
      $header = $this->assignments->get_last_active_header();
      $footer = $this->assignments->get_last_active_footer();

      if ( ! is_null( $layout ) ) {
        $items['tco-edit-layout-link'] = array(
          'title' => sprintf( csi18n('common.toolbar-edit-link'), csi18n('common.document.layout') ),
          'url' => $this->get_app_route_url( 'edit', [ 'id' => $layout->id(), 'url_context' => true ]),
          'group' => 'contextual'
        );
      }

      if ( ! is_null( $header ) ) {

        $items['tco-edit-header-link'] = array(
          'title' => sprintf( csi18n('common.toolbar-edit-link'), csi18n('common.document.header') ),
          'url' => $this->get_app_route_url( 'edit', [ 'id' => $header->id(), 'url_context' => true ] ),
          'group' => 'contextual'
        );
      }

      if ( ! is_null( $footer ) ) {
        $items['tco-edit-footer-link'] = array(
          'title' => sprintf( csi18n('common.toolbar-edit-link'), csi18n('common.document.footer') ),
          'url' => $this->get_app_route_url( 'edit', [ 'id' => $footer->id(), 'url_context' => true ] ),
          'group' => 'contextual'
        );
      }

    }



    $this->front_end_toolbar_items = $items;

  }

  public function addToolbarLinks() {

    if ( did_action( 'cornerstone_before_boot_app' ) ) {
      return;
    }

    global $wp_admin_bar;

    $permitted_items = array();


    $settings = [];
    $url = $this->get_app_route_url();

    // Launch button
    if ($this->permissions->userCanEditAnything()) {
      $settings['tco-cs-launch'] = [
        'title' => csi18n('app.launch'),
        'url' => $url,
        'group' => 'settings',
      ];
    }

    // Theme Options Button
    $settings ['tco-theme-options'] = [
      'title' => csi18n('app.theme-options.title'),
      'group' => 'settings',
      'capability' => 'manage_options',
      'url' => $this->get_app_route_url('theme-options'),
    ];

    // Templates / Design Cloud link
    if ( $this->permissions->userCan('template.manage_library') ) {
      $settings['tco-templates'] = array(
        'title' => csi18n( "common.title.template-manager" ),
        'url' => $this->get_app_route_url('design-cloud'),
        'group' => 'settings'
      );
    }

    // Settings button
    $settings ['tco-settings'] = [
      'title' => csi18n('admin.dashboard-settings-title'),
      'group' => 'settings',
      'capability' => 'manage_options',
      'url' => admin_url( 'admin.php?page=cornerstone-settings' )
    ];

    $items = array_merge(
      $this->front_end_toolbar_items,
      $settings,
      $this->get_custom_menu_items('toolbar')
    );

    $previous = null;
    $prev_index = 0;

    foreach ($items as $key => $value) {

      if ( isset( $value['capability'] ) && ! current_user_can( $value['capability'] ) ) {
        continue;
      }

      $item = array(
        'id'     => $key,
        'parent' => 'tco-main',
        'title'  => $value['title'],
        'href'   => $value['url'],
      );

      if ( ! is_null( $previous ) && $items[$previous]['group'] !== $value['group'] ) {
        if ( ! isset( $permitted_items[$prev_index]['meta'] ) ) {
          $permitted_items[$prev_index]['meta'] = array();
        }
        if ( ! isset( $permitted_items[$prev_index]['meta']['class'] ) ) {
          $permitted_items[$prev_index]['meta']['class'] = '';
        }
        $permitted_items[$prev_index]['meta']['class'] .= 'tco-ab-item-divider';
      }

      $previous = $key;

      $permitted_items[] = $item;
      $prev_index = count($permitted_items) - 1;

    }

    if ( empty( $permitted_items ) ) {
      return;
    }

    $logo = $this->make_menu_icon();
    // $logo = $logo ? "<span class=\"tco-admin-bar-logo\">$logo</span>" : '';
    $logo = $logo ? "<span class=\"tco-admin-bar-logo ab-item\" style=\"background-image: url($logo)\"></span>" : '';
    $title = csi18n('common.toolbar-title');

    $wp_admin_bar->add_menu( array(
      'id'    => 'tco-main',
      'title' => $logo . $title,
      'href'  => $this->get_app_route_url()
    ) );

    foreach ($permitted_items as $permitted_item) {
      $wp_admin_bar->add_menu( $permitted_item );
    }

  }

  public function dashboard_logo() {
    // shoooooooooould work...
    //
    // global $_wp_admin_css_colors;
    // $color = get_user_option( 'admin_color' );

    // if ( empty( $color ) || ! isset( $_wp_admin_css_colors[ $color ] ) ) {
    //   $color = 'fresh';
    // }

    // $fill = $_wp_admin_css_colors[ $color ]->icon_colors['base'];

    $fill = '#a7aaad';

    ob_start(); ?>

    <svg fill="<?php echo $fill; ?>" viewBox="0 0 792 780" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
      <g stroke="none" stroke-width="1" fill-rule="evenodd">
        <path d="M43.3636095,86.9641283 L736.363609,0.386827599 C763.490826,-3.00220732 788.229136,16.2413912 791.618171,43.3686079 C791.872478,45.4041834 792,47.4535991 792,49.5049985 L792,729.625941 C792,756.964036 769.838095,779.125941 742.5,779.125941 C740.653098,779.125941 738.807643,779.022576 736.972292,778.816331 L43.9722921,700.941331 C18.9307987,698.127325 -1.0349541e-13,676.950049 -1.0658141e-13,651.750941 L-1.13686838e-13,136.082299 C-1.16744203e-13,111.117019 18.5909046,90.0590113 43.3636095,86.9641283 Z M373.599475,463.342808 C355.383475,481.548777 328.059475,491.443326 303.903475,491.443326 C235.395475,491.443326 208.863475,443.553711 208.467475,397.643005 C208.071475,351.336517 236.979475,301.467992 303.903475,301.467992 C328.059475,301.467992 352.611475,309.779413 370.827475,327.5896 L405.675475,293.948135 C377.163475,265.847617 341.523475,251.599467 303.903475,251.599467 C203.715475,251.599467 156.591475,325.214909 156.987475,397.643005 C157.383475,469.675319 200.943475,540.520287 303.903475,540.520287 C343.899475,540.520287 380.727475,527.459483 409.239475,499.358965 L373.599475,463.342808 Z M638.919475,302.655338 C617.931475,259.910888 573.183475,247.641647 530.019475,247.641647 C478.935475,248.037429 422.703475,271.388564 422.703475,328.381164 C422.703475,390.51893 474.975475,405.558644 531.603475,412.286937 C568.431475,416.244756 595.755475,426.930869 595.755475,453.052477 C595.755475,483.131905 564.867475,494.609582 531.999475,494.609582 C498.339475,494.609582 466.263475,481.152995 453.987475,450.677786 L410.427475,473.237357 C431.019475,523.897446 474.579475,541.311851 531.207475,541.311851 C592.983475,541.311851 647.631475,514.794461 647.631475,453.052477 C647.631475,386.956892 593.775475,371.917178 535.959475,364.793103 C502.695475,360.835284 474.183475,354.106991 474.183475,329.964292 C474.183475,309.383631 492.795475,293.156571 531.603475,293.156571 C561.699475,293.156571 587.835475,308.196285 597.339475,324.027563 L638.919475,302.655338 Z"></path>
      </g>
    </svg>

    <?php return ob_get_clean();
  }

  /**
   * Load View files
   */

  public function notices() {

    $show_cornerstone_validation_notice = ( false === get_option( 'cornerstone_dismiss_validation_notice', false ) && ! $this->is_validated() && ! in_array( get_current_screen()->parent_base, apply_filters( 'cornerstone_validation_notice_blocked_screens', array( 'cornerstone-home' ) ) ) );

    if ( $show_cornerstone_validation_notice && ! apply_filters( '_cornerstone_integration_remove_global_validation_notice', false ) && ! defined( 'X_VERSION') ) {

      tco_common()->admin_notice( array(
        'message' => sprintf( csi18n('admin.validation-global-notice'), admin_url( 'admin.php?page=cornerstone-home' ) ),
        'dismissible' => true,
        'ajax_dismiss' => 'cs_dismiss_validation_notice'
      ) );

    }

  }

  public function make_menu_icon() {
    return 'data:image/svg+xml;base64,' . base64_encode( $this->dashboard_logo() );
  }

  //
  // Style admin menu items (Dashboard only)
  //

  public function get_admin_menu_css() {
    ob_start();
    ?>

      #adminmenu li.toplevel_page_cornerstone-home .wp-menu-image img,
      #adminmenu li.toplevel_page_cornerstone-settings .wp-menu-image img {
        width: 18px;
        height: auto;
        padding: 9px 0 0 0;
        opacity: 1;
      }
    <?php

    return ob_get_clean();
  }


  //
  // Other admin miscellaneous CSS
  //

  public function get_admin_miscellaneous_css() {
    ob_start();

    //Fix menu item's margin
    ?>
      .menu-item-settings .description-thin, .menu-item-settings .description-wide {
          margin-right: 10px !important;
      }
      .menu-item-settings .description-thin[class*="_alt"]:not([class*="image_alt"]),
      .menu-item-settings .description-thin[class*="_alt_alt"],
      .menu-item-settings .description-thin[class*="image_height"] {
          margin-right: 0 !important;
      }
    <?php

    return ob_get_clean();

  }

  //
  // Style admin bar items (Dashboard / Front End + Logged In)
  //

  public function admin_bar_css() {

    ob_start();
    ?>

      .tco-ab-item-divider:not(:last-child) {
        margin-bottom: 5px !important;
        border-bottom: 1px solid rgba(0,0,0,0.1);
        padding-bottom: 5px !important;
        box-shadow: 0 1px 0 rgba(255,255,255,0.05)
      }
      #adminmenu li.menu-top > .wp-submenu > li.tco-menu-divider:not(:last-child) {
        position: relative;
        margin-bottom: 5px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        padding-bottom: 6px;
        box-shadow: 0 1px 0 rgba(255, 255, 255, 0.05);
      }
      #wpadminbar .tco-admin-bar-logo svg {
        width: 19px;
        height: 19px;
        transform: translateY(4px);
      }
      #wp-admin-bar-tco-main {
        line-height: 1 !important;
      }
      #wp-admin-bar-tco-main .ab-item[aria-haspopup] {
        display: inline-flex !important;
        flex-flow: row nowrap !important;
        justify-content: flex-start !important;
        align-items: center !important;
      }
      #wp-admin-bar-tco-main .tco-admin-bar-logo.ab-item {
        float: left;
        transform: translateY(6px);
        display: block !important;
        width: 20px !important;
        height: 20px !important;
        margin-right: 6px !important;
        background-repeat: no-repeat;
      }

    <?php

    wp_add_inline_style( 'admin-bar', $this->minifyCss->run( ob_get_clean() ) );

  }





  public function admin_print_scripts() { ?>

    <script>
    jQuery(function($){

      // Add menu dividers
      $('[data-tco-admin-menu-divider]').closest('li').addClass('tco-menu-divider');

      // Fix WordPress "Theme Options" button
      var themeOptionsUrl = '<?php echo $this->get_app_route_url('theme-options'); ?>';

      $('body').on('click', '.button[href="themes.php?page=' + themeOptionsUrl + '"]', function( e ) {
        e.preventDefault();
        window.location.href = themeOptionsUrl;
      });

    });
    </script>

  <?php

  }


  /**
   * Get a URL that can be used to access Cornerstone for a given post.
   * @param  string $post Accepts a post object, or post ID. Uses queried object if one isn't provided
   * @return string
   */
  public function get_edit_url( $post = '' ) {

    $post = $this->permissions->contextualPost( $post );

    if ( !$post)
      return null;

    $url = $this->get_app_route_url( 'edit', [ 'id' => $post->ID ]  );

    if ( force_ssl_admin() ) {
      $url = preg_replace( '#^http://#', 'https://', $url );
    }

    return $url;

  }

  public function get_app_route_url( $route = '', $params = [] ) {

    $doc_id = isset( $params['id'] ) ? $params['id'] : '';
    $type = isset( $params['type'] ) ? $params['type'] : '';

    if ( ! $this->permalinks->hasValidStructure() ) {
      $args = array('cs-launch' => 1);
      if ( $route ) {
        $args['cs_route'] = esc_attr($route);
        if ( $type ) {
          $args['cs_route'] .= ".$type";
        }
        if ( $doc_id ) {
          $args['cs_route'] .= "/$doc_id";
        }
      }
      return add_query_arg( $args, home_url() );
    }

    $url = $this->settings->appUrl();

    if ( $route ) {
      $route = str_replace('.', '/', $route );
      $url .= "/$route";
      if ( $doc_id ) {
        $url .= "/$doc_id";
      }
    }

    if ( isset( $params['url_context'] ) && $params['url_context']) {
      global $wp;
      if ( ! is_admin() && $wp->request ) {
        return add_query_arg( [ 'url' => esc_url( home_url( $wp->request ) ) ], $url);
      }
    }

    return esc_url( $url );

  }

}
