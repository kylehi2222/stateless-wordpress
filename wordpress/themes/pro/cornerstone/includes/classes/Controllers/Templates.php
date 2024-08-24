<?php

namespace Themeco\Cornerstone\Controllers;

use DomainException;
use Themeco\Cornerstone\Services\Routes;
use Themeco\Cornerstone\Services\Permissions;
use Themeco\Cornerstone\Services\Templates as TemplateService;
use Themeco\Cornerstone\Documents\Document;
use Themeco\Cornerstone\Services\RemoteAssets;
use Themeco\Cornerstone\Templating\Template;
use Themeco\Cornerstone\Util\Endpoint;


class Templates {
  private $routes;
  private $permissions;
  private $templates;
  private $remoteAssets;

  public function __construct(Routes $routes, Permissions $permissions, TemplateService $templates, RemoteAssets $remoteAssets) {
    $this->routes = $routes;
    $this->permissions = $permissions;
    $this->templates = $templates;
    $this->remoteAssets = $remoteAssets;
  }

  public function setup() {

    $this->routes->add_route('get', 'template-index', [$this, 'getAll']);
    $this->routes->add_route('get', 'template-item', [$this, 'getItem']);
    $this->routes->add_route('get', 'template-item-full', [$this, 'getItemFull']);
    $this->routes->add_route('post', 'template-item-create', [$this, 'createItem']);
    $this->routes->add_route('post', 'templates-import', [$this, 'import']);
    $this->routes->add_route('post', 'templates-dependency', [$this, 'importDependency']);
    $this->routes->add_route('post', 'templates-terms', [$this, 'importTerms']);
    $this->routes->add_route('post', 'templates-delete', [$this, 'deleteItems']);
    $this->routes->add_route('post', 'template-item-update', [$this, 'updateItem'] );
    $this->routes->add_route('post', 'templates-menu-import', [$this, 'importMenu'] );

    $this->routes->add_route('post', 'template-remote', [$this, 'getRemoteItem']);

  }

  public function getAll() {
    return $this->templates->query();
  }

  public function _getItem( $params, $full) {
    if (! isset($params['id'])) {
      throw new \Exception( 'Invalid params' );
    }

    $template = Template::locate( $params['id'] );

    if ( ! $template ) {
      throw new \Exception( 'Could not locate template' );
    }

    if ($full) {
      $template->loadMeta();
    }

    return $template->serialize();;
  }

  public function getItem( $params ) {
    return $this->_getItem( $params, false );
  }

  public function getItemFull( $params ) {
    return $this->_getItem( $params, true );
  }

  public function locateExistingDependency( $hash ) {
    global $wpdb;
    $results = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_cs_import' AND meta_value = %s", $hash ) );
    return $results ? Document::locate((int) $results[0]->post_id ): null;
  }

  public function importTerms( $params ) {
    if (
      ! $this->permissions->userCan('template.manage_library')
      && ! $this->permissions->userCan('template.import_terms')
    ) {
      throw new \Exception( 'Unauthorized' );
    }

    if ( ! isset( $params['terms'] ) ) {
      throw new \Exception( 'Incomplete data' );
    }

    $result = [];
    foreach ($params['terms'] as $hash => $term) {
      $existing_term = get_term_by( 'name', $term['name'], $term['taxonomy'] );

      if ( $existing_term ) {

        $term_id = (int) $existing_term->term_id;

        wp_update_term( $term_id, $term['taxonomy'], array(
          'description' => $term['description'],
          'slug' => $term['slug']
        ));

        $result[$hash] = $term_id;

      } else {

        $newTerm = wp_insert_term( $term['name'], $term['taxonomy'], array(
          'description' => $term['description'],
          'slug' => $term['slug']
        ));

        $result[$hash] = (int) $newTerm['term_id'];
      }

    }

    return $result;

  }

  public function importDependency( $params ) {
    if ( ! $this->permissions->userCan('template.manage_library') ) {
      throw new \Exception( 'Unauthorized' );
    }

    if ( ! isset( $params['data'] ) || ! isset( $params['data']['sig']) ) {
      throw new \Exception( 'Incomplete data' );
    }

    $existing = $this->locateExistingDependency( $params['data']['sig'] );

    // Found existing signature and that signature is the same type
    if ( $existing && $existing->getDocType() === $params['data']['subType']) {
      // In case the post is in the trash
      // it will put it in limbo without this
      wp_untrash_post($existing->id());

      $status = $existing->getPublishStatusType();

      wp_update_post([
        'ID' => $existing->id(),
        'post_status' => $status,
      ]);

      $hasSaved = !empty(get_post_meta($existing->id(), '_cs_last_save'));

      // Replace content if replace strategy setup
      if ( isset( $params['strategy'] ) && $params['strategy'] === 'replace' && !$hasSaved) {
        $existing->update([
          'elements' => $params['data']['meta']['elements'],
          'settings' => $params['data']['meta']['settings']
        ]);
        $existing->save();
      }
      return $existing->id();
    }

    $doc = Document::create( $params['data']['subType'] );
    $doc->update([
      'title' => $params['data']['title'],
      'elements' => $params['data']['meta']['elements'],
      'settings' => $params['data']['meta']['settings']
    ]);

    $doc->save();
    $id = $doc->id();

    if ( isset($params['fullSite']) && $params['fullSite'] ) {

      if ( isset( $params['data']['page_on_front'] ) ) {
        update_option('page_on_front', $id );
      }

      if ( isset( $params['data']['page_for_posts'] ) ) {
        update_option('page_for_posts', $id );
      }
    }

    // Update thumbnail
    if ( isset( $params['data']['thumbnail']) ) {
      set_post_thumbnail(
        $id,
        (int)$params['data']['thumbnail']
      );
    }

    if ( isset( $params['data']['terms']) ) {

    }

    if ( isset( $params['data']['parent']) ) {

    }

    update_post_meta( $id, '_cs_import', $params['data']['sig'] );

    return $id;


  }

  /**
   * Import menu and create nav menu items
   */
  public function importMenu($params) {
    if ( ! $this->permissions->userCan('template.manage_library') ) {
      throw new \Exception( 'Unauthorized' );
    }

    $menu = cs_get_array_value($params, 'menu', []);

    if (empty($menu['name'])) {
      throw new DomainException('Invalid menu sent to menu import');
    }

    // Already has a menu with name
    $foundMenus = wp_get_nav_menus([ 'name' => $menu['name'] ]);

    if (!empty($foundMenus)) {
      $foundMenuID = $foundMenus[0]->term_id;
      $this->setMenuAsMain($foundMenuID);
      return $foundMenus;
    }

    // Create initial menu
    $created = wp_create_nav_menu($menu['name']);

    $this->setMenuAsMain($created);

    if (!$created) {
      throw new DomainException('Could not create nav menu ' . $menu['name']);
    }

    // Loop and add new items
    foreach ($menu['items'] as $item) {
      $this->importMenuItem($created, $item);
    }

    return $created;
  }

  /**
   * Import menu item entry
   */
  public function importMenuItem($created, $item, $parentID = null) {
    // Menu item is a post and should grab the new post ID
    if ($item['menu-item-object'] !== 'custom') {
      $existing = $this->locateExistingDependency( $item['menu-item-object-id'] );

      if (empty($existing)) {
        trigger_error('Could not locate existing dependency when creating nav menu item ' . $item['menu-item-object-id']);
        return;
      }

      $item['menu-item-object-id'] = $existing->id();
    }

    $item['menu-item-parent-id'] = $parentID;

    // Update nav menu item creating
    $menuItemID = wp_update_nav_menu_item($created, 0, $item);

    // Import child menu items
    if (!empty($item['children'])) {
      foreach ($item['children'] as $child) {
        $this->importMenuItem($created, $child, $menuItemID);
      }
    }
  }

  /**
   * Sets a menu as the primary and footer
   */
  private function setMenuAsMain($menuID) {
    $locations = get_nav_menu_locations();
    $locations['menu-header'] = $menuID;
    $locations['menu-footer'] = $menuID;
    $locations['primary'] = $menuID;
    $locations['footer'] = $menuID;
    set_theme_mod( 'nav_menu_locations', $locations );
  }

  public function import($params) {

    $save = isset( $params['saveToLibrary'] ) && $params['saveToLibrary'];

    if (
      ! $this->permissions->userCan('template.manage_library')
      && (
        ! $save
        && ! $this->permissions->userCan('template.insert')
      )
    ) {
      throw new \Exception( 'Unauthorized' );
    }

    if ( ! isset( $params['items'] ) ) {
      throw new \Exception( 'Attempting to create template without items' );
    }

    $titles = [];
    if ($save) {
      global $wpdb;
      $titles = $wpdb->get_col( "SELECT post_title FROM $wpdb->posts WHERE post_type = 'cs_template'" );
    }
    $results = [];
    foreach ( $params['items'] as $item ) {
      if ( ! isset( $item['type'] ) || ! isset( $item['subType'] ) ) {
        continue;
      }

      $record = Template::create($item['type'], $item['subType'], !$save );

      if ( isset( $item['title'] ) ) {
        if ($save) {
          $original = $item['title'];
          $index = 2;
          while ( in_array( $item['title'], $titles ) ) {
            $item['title'] = str_replace('{{index}}', $index++, str_replace('{{label}}', $original, csi18n('common.indexed')));
          }
        }
        $record->setTitle( $item['title'] );
      }

      if ( isset( $item['preview'] ) ) {
        $record->setPreview( $item['preview'] );
      }

      if ( isset( $item['meta'] ) ) {
        $record->setMeta( $item['meta'] );
      }

      // Thumbnail sent from a document type
      // that is being imported as template
      if (isset($item['thumbnail'])) {
        $record->setThumbnail( $item['thumbnail'] );
      }

      $results[] = $record->serializeFull();

      if ($save) {
        $record->save();
      }

    }

    return [ 'items' => $results ];

  }

  public function createItem($params) {

    if ( ! $this->permissions->userCan('template.manage_library') ) {
      throw new \Exception( 'Unauthorized' );
    }

    if ( ! isset( $params['type'] ) ) {
      throw new \Exception( 'Attempting to create template without specifying a type.' );
    }

    if ( ! isset( $params['subType'] ) ) {
      throw new \Exception( 'Attempting to create template without specifying a subtype.' );
    }

    $record = Template::create($params['type'], $params['subType']);


    if ( isset( $params['title'] ) ) {
      $record->setTitle( $params['title'] );
    }

    if ( isset( $params['preview'] ) ) {
      $record->setPreview( $params['preview'] );
    }

    if ( isset( $params['meta'] ) ) {
      $record->setMeta( $params['meta'] );
    }

    return $record->save();

  }

  public function updateItem( $params ) {


    if ( ! $this->permissions->userCan('template.manage_library') ) {
      throw new \Exception( 'Unauthorized' );
    }

    if ( ! isset( $params['id'] ) ) {
      throw new \Exception( 'Attempting to update Template without specifying an ID.' );
    }

    if ( ! $this->permissions->userCan('template.manage_library') ) {
      throw new \Exception( 'Unauthorized' );
    }

    $template = Template::locate(  (int) $params['id']);

    if ( ! $template ) {
      throw new \Exception( 'Invalid template' );
    }

    if ( isset( $params['title'] ) ) {
      $template->setTitle( $params['title'] );
    }

    if ( isset( $params['preview'] ) ) {
      $template->setPreview( $params['preview'] );
    }

    if ( isset( $params['meta'] ) ) {
      $template->setMeta( $params['meta'] );
    }

    return $template->save();
  }

  public function deleteItems( $params ) {

    if ( ! $this->permissions->userCan('template.manage_library') ) {
      throw new \Exception( 'Unauthorized' );
    }

    if (! isset($params['ids'])) {
      throw new \Exception( 'Ids to delete missing' );
    }

    foreach ( $params['ids'] as $id ) {
      $template = Template::locate(  (int) $id);
      $template->delete();
    }

    return array( 'success' => true );
  }

  public function getRemoteItem( $params ) {
    if ( ! $this->permissions->userCan('template') ) {
      throw new \Exception( 'Unauthorized' );
    }

    if ( isset($params['site'])) {
      $this->remoteAssets->proxyFile( '/site/' . $params['site'] );
    }

    if ( isset($params['asset'])) {
      // Filter and expand if needed
      // used by packs
      $params['asset'] = apply_filters('cs_remote_asset_path', $params['asset']);

      $url = strpos($params['asset'], "http") === 0
        ? $params['asset']
        : "/asset/" . $params['asset'];

      $this->remoteAssets->proxyFile($url);
    }

    throw new \Exception( 'Asset id missing' );
  }


  // public function export_locate_image_ids( $data ) {

  //   if ( ! $this->permissions->userCan('template.manage_library') ) {
  //     throw new \Exception( 'Unauthorized' );
  //   }

  //   if ( ! isset( $data['hashMap'] ) ) {
  //     throw new \Exception( 'Ids to locate missing.' );
  //   }

  //   foreach ( $data['hashMap'] as $hash => $source) {
  //     $img_atts = cs_apply_image_atts( [ 'src' => $source, 'size' => null ]);
  //     $resolved[$hash] = isset( $img_atts['src'] ) && $img_atts['src'] ? $img_atts['src'] : null;
  //   }

  //   return array(
  //     'resolved' => $resolved,
  //     'success'  => true
  //   );

  // }

  // protected function find_more_global_blocks( $elements ) {

  //   $more = array();

  //   foreach( $elements as $element ) {

  //     if ( ! isset( $element['_type'] ) ) {
  //       continue;
  //     }

  //     if ( isset( $element['_modules'] ) ) {
  //       $more = array_merge($more, $this->find_more_global_blocks( $element['_modules']) );
  //     }

  //     if ( 'global-block' === $element['_type'] && isset( $element['global_block_id']) ) {
  //       array_push($more, $element['global_block_id']);
  //     }

  //   }

  //   return array_unique( $more );

  // }


  // public function import_items( $data ) {

  //   if ( ! $this->permissions->userCan('template.manage_library') ) {
  //     throw new \Exception( 'Unauthorized' );
  //   }

  //   if ( ! isset( $data['packageSignature'] ) ) {
  //     throw new \Exception( 'Package signature missing.' );
  //   }

  //   if ( ! isset( $data['files'] ) || ! $this->validate_import_files( $data['files'] ) ) {
  //     throw new \Exception( 'Files failed validation.' );
  //   }

  //   $response = [ 'done' => true, 'templates' => [] ];

  //   foreach ($data['files'] as $file) {

  //     if ( 'template' === $file['type'] ) {
  //       $template_data = $file['data'];
  //       $template_data['package_signature'] = $data['packageSignature'];
  //       $template = new \Cornerstone_Template( $template_data );
  //       if ( isset( $data['saveToLibrary'] ) && $data['saveToLibrary'] ) {
  //         $template->save();
  //       } else {
  //         $response['templates'][] = $template->serialize();
  //       }

  //     }

  //     if ( 'global-block' === $file['type'] ) {

  //       Document::locate((int) $file['data']['id'])->update([
  //         'elements' => $file['data']['elements'],
  //         'settings' => $file['data']['settings']
  //       ])->save();

  //     }

  //   }

  //   return $response;

  // }



  // protected function validate_import_files( $files ) {

  //   foreach ($files as $file) {

  //     if ( ! isset( $file['type'] ) ) {
  //       return false;
  //     }

  //   }

  //   return true;

  // }

  // public function prepare_global_blocks_import( $data ) {

  //   if ( ! $this->permissions->userCan('template.manage_library') ) {
  //     throw new \Exception( 'Unauthorized' );
  //   }

  //   $global_blocks = array();

  //   if ( ! isset( $data['globalBlockRequests'] ) ) {
  //     throw new \Exception( 'No global blocks' );
  //   }

  //   foreach ($data['globalBlockRequests'] as $global_block_request) {

  //     $global_block = Document::create('custom:global-block-compat')->update([
  //       'title'       => $global_block_request['title']
  //     ]);

  //     $global_block->save();

  //     $global_blocks[$global_block_request['id']] = $global_block->id();

  //   }

  //   return array(
  //     'globalBlockIDs' => $global_blocks,
  //     'success'        => true
  //   );

  // }

}
