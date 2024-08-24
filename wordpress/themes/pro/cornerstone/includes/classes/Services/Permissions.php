<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Plugin;

class Permissions implements Service {

  public $_content_types;
  protected $roleDefaults;
  protected $cache = array();

  protected $elements;

  public function defaultPolicy() {
    return [

      'administrator' => [

        'content.page' => true,
        'content.post' => false,
        'layout' => true,
        'component' => true,

        'global' => true,
        'template' => true,
        'element-library' => true,

        'preferences' => true,
        'global.export_documents' => true,
      ],

      '_default' => array(

        'content.{post-type}' => false,
        'content.{post-type}.create' => true,
        'content.{post-type}.delete' => true,
        'content.{post-type}.element_locking' => true,
        'content.{post-type}.edit_custom_css' => true,
        'content.{post-type}.edit_custom_js' => true,

        'layout'        => false,
        'layout.create' => true,
        'layout.delete' => true,
        'layout.element_locking' => true,
        'layout.edit_custom_css' => true,
        'layout.edit_custom_js' => true,

        'component' => false,
        'component.create' => true,
        'component.delete' => true,
        'component.element_locking' => true,
        'component.edit_custom_css' => true,
        'component.edit_custom_js' => true,

        'template' => false,
        'template.manage_library' => true,
        'template.save' => true,
        'template.insert' => true,
        'template.import_terms' => true,
        'template.themeco_templates' => true,
        'template.max' => true,

        'global'        => false,
        'global.colors' => true,
        'global.fonts' => true,
        'global.theme_options' => true,
        'global.theme_options_import' => true,
        'global.theme_options_export' => true,
        'global.export_documents' => false,
        'global.edit_custom_css' => true,
        'global.edit_custom_js' => true,
        'global.element_locking' => true,
        'global.apply_preset' => true,
        'global.save_preset' => true,

        'preferences' => true,
        'preferences.update' => true,

        'element-library' => false,
        'element-library.classic' => false,

        'element-library.{id}'                    => true,
        'element-library.{id}.inspect'            => true,
        'element-library.{id}.copy'               => true,
        'element-library.{id}.paste'              => true,
        'element-library.{id}.paste_style'        => true,
        'element-library.{id}.clear_style'        => true,
        'element-library.{id}.apply_preset'       => true,
        'element-library.{id}.delete'             => true,
        'element-library.{id}.manage'             => true,
        'element-library.{id}.save_preset'        => true,
        'element-library.{id}.show_in_library'    => true,

        'element.{id}'                    => true,
        'element.{id}.inspect'            => true,
        'element.{id}.copy'               => true,
        'element.{id}.paste'              => true,
        'element.{id}.paste_style'        => true,
        'element.{id}.clear_style'        => true,
        'element.{id}.apply_preset'       => true,
        'element.{id}.save_preset'        => true,
        'element.{id}.show_in_library'    => true,
      )

    ];

  }

  public function __construct(Elements $elements) {
    $this->elements = $elements;
  }

  public function getRoleDefaults() {
    if ( ! isset( $this->roleDefaults ) ) {
      $this->roleDefaults = apply_filters( 'cs_app_permission_defaults', $this->hideDeprecatedElements( $this->defaultPolicy() ) );
    }
    return $this->roleDefaults;
  }

  // Deprecated elements permission setups
  public function hideDeprecatedElements( $defaults ) {

    $definitions = $this->elements->get_all_elements();
    $deprecatedEnabled = apply_filters(
      "cs_elements_deprecated_enabled_default",
      false
    );

    foreach ($definitions as $name => $definition) {
      if ( $definition->get_group() === 'deprecated' ) {
        $defaults['_default']["element-library.$name"] = $deprecatedEnabled;
      }
    }

    return $defaults;
  }

  public function getStoredPermissions() {
    $stored = get_option('cs_permissions', []);

    $defaults = $this->getRoleDefaults();

    $migrated = [];

    foreach ($stored as $role => $permissions) {
      $migratedRole = [];
      $hasLayouts = false;
      $canEdit = false;
      foreach ($permissions as $permission => $state) {

        // Migrate Elements

        if (strpos($permission, 'element.' ) === 0) {

          $parts = explode('.',$permission);
          if (strpos($parts[1], 'classic:' ) !== 0) {
            $migratedRole['element-library.' . $parts[1]] = $state;
          }
          continue;
        }

        // Migrate Content & Global Blocks

        if ( $permission === 'content.cs_global_block') {
          $migratedRole['component'] = $state;
          continue;
        }

        if (strpos($permission, 'content.' ) === 0) {
          $parts = explode('.',$permission);
          $post_type  = $parts[1];
          if ($post_type !== 'cs_global_block') {
            $parts[1] = '{post-type}';
            $generic_permission = implode('.', $parts);
            if ( isset( $defaults['_default'][$generic_permission])) {
              $migratedRole[$permission] = $state;
              if ($state) {
                $canEdit = true;
              }
            }
          }
          continue;
        }


        // Migrate Headers / Footers to Layouts
        if (strpos($permission, 'headers' ) === 0) {
          if ($permission === 'headers' && $state ) {
            $hasLayouts = true;
            $canEdit = true;
          }
          continue;
        }

        if (strpos($permission, 'footers' ) === 0) {
          if ($permission === 'footers' && $state ) {
            $hasLayouts = true;
            $canEdit = true;
          }
          continue;
        }

        if ( $permission === 'theme_options' ) {
          $migratedRole['global'] = $state;
          $migratedRole['global.theme_options'] = $state;
          continue;
        }

        if ( $permission === 'theme_options.edit_custom_css' ) {
          $migratedRole['global.edit_custom_css'] = $state;
          continue;
        }

        if ( $permission === 'theme_options.edit_custom_js' ) {
          $migratedRole['global.edit_custom_js'] = $state;
          continue;
        }

        if ( $permission === 'colors' ) {
          $migratedRole['global.colors'] = $state;
          continue;
        }

        if ( $permission === 'fonts' ) {
          $migratedRole['global.fonts'] = $state;
          continue;
        }

        if ( isset( $defaults['_default'][$permission] ) || strpos($permission, 'element-library.' ) === 0 ) {
          $migratedRole[$permission] = $state;
        }

      }

      if ($hasLayouts && (! isset($defaults[$role]['layout']) || ! $defaults[$role]['layout'])) {
        $migratedRole['layout'] = true;
      }

      if ($canEdit) {
        if (! isset($defaults[$role]['template']) || ! $defaults[$role]['template']) {
          $migratedRole['template'] = true;
        }
      }

      // Setup role
      $migrated[$role] = $migratedRole;
    }


    return $migrated;
  }

  public function getAppData() {
    return array(
      'roles'       => cs_get_wp_roles_options(),
      'permissions' => $this->getStoredPermissions(),
      'defaults'    => $this->getRoleDefaults(),
      'controls' => $this->getControls(),
      'elements' => $this->getElementOptions(),
    );
  }

  public function updateStoredPermissions($update) {
    return empty( $update ) ? delete_option('cs_permissions') : update_option('cs_permissions', $update);
  }

  // Update singular permission
  public function updateStoredPermission($role, $key, $value) {
    $permissions = $this->getStoredPermissions();

    if (empty($permissions[$role])) {
      $permissions[$role] = [];
    }

    $permissions[$role][$key] = $value;

    return $this->updateStoredPermissions($permissions);
  }

  public function getUserPermissions( $user_id = null ) {

    if ( ! $user_id ) {
      $user_id = get_current_user_id();
    }

    if ( ! $user_id ) {
      return array();
    }

    $user_key = "u$user_id";

    $roles = $this->getUserRoles( $user_id );

    if ( ! isset( $this->cache[$user_key] ) ) {
      $this->cache[$user_key] = $this->getRolePermissions( $roles );
      $this->cache[$user_key]['manage_options'] = user_can( $user_id, 'manage_options');
      $this->cache[$user_key]['unfiltered_html'] = user_can( $user_id, 'unfiltered_html');
    }

    return $this->cache[$user_key];

  }

  public function userCan( $permission, $user_id = null ) {

    if ( ! is_string( $permission ) ) {
      return false;
    }

    $permissions = $this->getUserPermissions( $user_id );

    $parts = explode('.', $permission);

    $next = array();
    $dynamic = array(
      'content' => '{post-type}',
      'element' => '{id}'
    );

    $implicit = ['element-library'];
    $siteBuilder = ['layout'];

    if ( isset( $parts[0]) && in_array( $parts[0], $siteBuilder ) && ! cornerstone('Env')->isSiteBuilder()) {
      return false;
    }

    while ( count( $parts ) ) {

      $segment = array_shift( $parts );
      $check = implode('.', $next) . ".$segment";
      $check = trim($check, '.');

      if ( ! isset( $permissions[$check] ) && isset( $next[0] ) && isset( $dynamic[$next[0]] ) ) {
        $test = array( $next[0], $dynamic[$next[0]] );
        if ( count( $next ) > 1 ) {
          $test[] = $segment;
        }
        $check = implode('.', $test );

      }

      $next[] = $segment;

      $test = isset( $permissions[$check] ) ? $permissions[$check] : null;
      if ( ! in_array( $next[0], $implicit, true) ) {
        $test = (bool) $test;
      }

      if ( ! isset( $dynamic[$segment] ) && false === $test ) {
        return false;
      }

    }

    return true;

  }

  public function getUserPostTypes( $user = null ) {

    $permissions = $this->getUserPermissions( $user );
    $types = array();

    if ( is_null( $user ) ) {
      $user = get_current_user_id();
    }


    foreach ($permissions as $key => $value) {

      if ( 0 !== strpos( $key, 'content.' ) || ! $value ) {
        continue;
      }

      $parts = explode('.', $key);

      if ( count( $parts ) > 2 || in_array( $parts[1], array( '{post-type}', 'cs_global_block'), true ) ) {
        continue;
      }

      // Ensure type exists. (Post type previously saved, but plugin is no longer active)
      $post_type = get_post_type_object($parts[1]);

      if ( $post_type && user_can( $user, $post_type->cap->edit_posts) ) {
        $types[] = $parts[1];
      }

    }

    return $types;

  }

  public function userCanAccessPostType( $post = '') {
    $post_type = '';

    if ( is_string( $post ) ) {
      $post_type = $post;
    }

    if ( ! $post ) {
      $post = $this->contextualPost();
      if ( ! $post ) {
        return false;
      }
    }

    if ( is_a( $post, 'WP_Post' ) ) {
      $post_type_obj = get_post_type_object( $post->post_type );
      $caps = (array) $post_type_obj->cap;
      if ( ! current_user_can( $caps['edit_post'], $post->ID ) ) {
        return false;
      }

      $post_type = $post->post_type;
    }

    $allowed = $this->getUserPostTypes();

		return in_array( $post_type, $allowed, true );

  }

  /**
   * administrator helper for roleHasPermission
   */
  public function adminHasPermission($permission) {
    return $this->roleHasPermission($permission, "administrator");
  }

  /**
   * Given a role does a permission exist for it
   */
  public function roleHasPermission($permission, $role) {
    $roleDefaults = $this->getRoleDefaults();
    $defaults = !empty($roleDefaults[$role]) ? $roleDefaults[$role] : [];

    $stored = $this->getStoredPermissions();
    $storedRole = !empty($stored[$role]) ? $stored[$role] : [];

    $permissions = array_merge($defaults, $storedRole);

    return !empty($permissions[$permission]);
  }

  public function getRolePermissions( $roles ) {

    if ( is_string( $roles ) ) {
      $roles = array( $roles );
    }

    $stored = $this->getStoredPermissions();
    $role_permissions = array();
    $merged = array();

    $role_defaults = $this->getRoleDefaults();
    foreach ($roles as $role) {
      $default_role = isset( $role_defaults[$role] ) ? $role_defaults[$role] : array();
      $default_role = array_merge($role_defaults['_default'], $default_role );
      $stored_role = isset( $stored[$role] ) ? $stored[$role] : array();
      $role_permissions[$role] = array_merge($default_role,$stored_role);
    }

    // If a user has multiple roles, merge the permissions together.

    foreach ($role_permissions as $permissions) {

      ksort($permissions);

      foreach ($permissions as $key => $value) {

        $parts = explode('.', $key);

        // If multiple rows share a top level key allow either role to enable the feature
        if ( 1 !== count($parts) &&
          isset($merged[$parts[0]]) && // Only merge a nested permission if the parent of this role has turned it on
          $merged[$parts[0]] !== isset( $permissions[$parts[0]] )
        ) {
          continue;
        }

        $merged[$key] = ( isset($merged[$key])) ? $merged[$key] || $value : $value;

      }
    }

    ksort( $merged);

    return $merged;

  }

  public function getUserRoles( $user_id ) {

    $roles = array();

    global $wpdb;

    $caps = get_user_meta( $user_id, $wpdb->get_blog_prefix() . 'capabilities', true );

    $wp_roles = wp_roles();

		if ( is_array( $caps ) ) {
      $roles = array_filter( array_keys( $caps ), array( $wp_roles, 'is_role' ) );
    }

    if ( is_super_admin() && ! in_array( 'administrator', $roles, true ) ) {
      $roles[] = 'administrator';
    }

    return $roles;

  }

  protected function getPostTypes() {

    if ( ! isset( $this->_content_types ) ) {

      $post_types = get_post_types( apply_filters( 'cs_get_content_types_args', array(
        'public'   => true,
        'show_ui' => true,
      ) ), 'objects' );

      unset( $post_types['attachment'] );

      $this->_content_types = array();

      foreach ( $post_types as $name => $post_type ) {
        $this->_content_types[$name] = ( isset( $post_type->labels->name ) ) ? $post_type->labels->name : $name;
      }

    }

    return $this->_content_types;

  }

  public function getControls() {

    $post_types = $this->getPostTypes();
    $documentTypes = [];

    if ( isset( $post_types['page'] ) ) {
      $documentTypes['content.page'] = $post_types['page'];
      unset( $post_types['page'] ); // ensure page is first
    }

    foreach ($post_types as $name => $title) {
      $documentTypes["content.$name"] = $title;
    }

    if ( cornerstone('Env')->isSiteBuilder() ) {
      $documentTypes['layout']    = csi18n('common.document.layouts');
    }

    $documentTypes['component'] = csi18n('common.document.components');

    $docControls = array();

    foreach ($documentTypes as $key => $value) {
      $docControls[] = array(
        'id'      => $key,
        'context' => 'document',
        'title'   => $value,
        'options' => $this->getContextKeys( $key )
      );
    }

    $controls = [
      [
        'id' => 'element-library',
        'title' =>  csi18n('admin.permissions.element-library'),
      ],
      [
        'id' => 'template',
        'title' =>  csi18n('admin.permissions.templates'),
        'options' => [
          'save',
          'insert',
          'import_terms',
          'manage_library',
          'themeco_templates',
          'max',
        ]
      ],
      [
        'id' => 'global',
        'title' =>  csi18n('admin.permissions.global'),
        'options' => [
          'theme_options',
          'theme_options_import',
          'theme_options_export',
          'export_documents',
          'colors',
          'fonts',
          'edit_custom_css',
          'edit_custom_js',
          'apply_preset',
        ]
      ],
      [
        'id' => 'preferences',
        'title' =>  csi18n('app.preferences.title'),
        'options' => [
          'update',
        ]
      ],
    ];

    return array_merge( $docControls, $controls );

  }

  public function getElementOptions() {

    $options = array();
    $context_defaults = $this->getContextKeys('element-library.{id}');
    $elements = cornerstone('Elements')->get_public_definitions();

    foreach ( $elements as $element ) {

      if ( $element->is_classic() ) {
        continue;
      }

      $title = $element->get_title();
      $title = $element->get_group() === "deprecated"
        ? sprintf("%s (Deprecated)", $title)
        : $title;

      $id = 'element-library.' . $element->get_type();
      $options[] = array(
        'id'   => $id,
        'context' => $id,
        'title'   => $title,
        'options' => $context_defaults,
      );

    }

    return $options;

  }


  public function getContextKeys( $context ) {

    $dynamic = array(
      'element-library' => 'element-library.{id}',
      'content' => 'content.{post-type}'
    );

    $context_keys = array();

    $role_defaults = $this->getRoleDefaults();
    $keys = array_keys( $role_defaults['_default'] );

    $parts = explode('.', $context);

    foreach ($keys as $key) {

      if ( 0 === strpos( $key, "$context." ) ) {
        $context_keys[] = str_replace("$context.", "",$key );
      }

      if ( isset($dynamic[$parts[0]]) && 0 === strpos( $key, $dynamic[$parts[0]] . '.' ) ) {
        $context_keys[] = str_replace( $dynamic[$parts[0]] . '.', "",$key );
      }

    }

    return array_values( array_unique( $context_keys ) );
  }

  /**
   * Get a WP_Post object from an ID or an automatic source
   * If $post_id is left black, it will be automatically populated (works in dashboard or on front end)
   * @param  string $post_id
   * @return \WP_Post
   */
  public function contextualPost( $post_id = '') {

    // Allow pass through of full post objects
    if ( is_object($post_id) && isset( $post_id->ID ) )
      return $post_id;

    // Get post by ID
    if ( is_int( $post_id ) )
      return get_post( $post_id );

    // Or, in the dashboard use a query string
    if ( is_admin() && isset($_GET['post']) )
      return get_post( $_GET['post'] );

    // Or, use the queried object
    if ( '' == $post_id ) {
      $post = get_queried_object();
      if ( is_a( $post, 'WP_POST' ))
        return $post;
    }

    // Otherwise there's just no way...
    return false;

  }

  public function getPostCapability( $post_id, $cap ) {

    $post = $this->contextualPost( $post_id );

    if ( ! is_a( $post, 'WP_POST' ) ) {
      return $cap;
    }

    return $this->getPostTypeCapability( $post->post_type, $cap );

  }

  public function getPostTypeCapability( $post_type, $cap ) {

    $post_type_object = get_post_type_object( $post_type );
    $caps = (array) $post_type_object->cap;
    return $caps[ $cap ];

  }

  /**
   * Helper for checking if has any cornerstone edit permissions
   */
  public function userCanEditAnything() {
    $permissions = $this->getUserPermissions();

    return !empty($permissions['content.page'])
      || !empty($permissions['content.post'])
      || !empty($permissions['component'])
      || !empty($permissions['layout'])
      || !empty($permissions['template'])
      || !empty($permissions['global']);
  }
}
