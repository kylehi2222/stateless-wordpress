<?php

namespace Themeco\Cornerstone\Controllers;

use Themeco\Cornerstone\Plugin;
use Themeco\Cornerstone\Services\Routes;
use Themeco\Cornerstone\Services\Components;
use Themeco\Cornerstone\Services\Permissions;
use Themeco\Cornerstone\Services\Locator;
use Themeco\Cornerstone\Services\Wpml;
use Themeco\Cornerstone\Documents\Document;
class Documents {
  public $components;
  public $routes;
  public $permissions;
  public $locator;
  public $wpml;

  public function __construct(Components $components, Routes $routes, Permissions $permissions, Locator $locator, Wpml $wpml) {
    $this->components = $components;
    $this->routes = $routes;
    $this->permissions = $permissions;
    $this->locator = $locator;
    $this->wpml = $wpml;
  }

  public function setup() {
    $this->routes->add_route('get', 'document-index',      [$this, 'getDocuments']);
    $this->routes->add_route('get', 'document-index-full', [$this, 'getAllDocuments']);
    $this->routes->add_route('get', 'document-search',     [$this, 'searchDocuments']);

    $this->routes->add_route('get', 'document', [$this, 'read']);
    $this->routes->add_route('post', 'document-delete', [$this, 'delete']);
    $this->routes->add_route('post', 'document-create', [$this, 'create']);
    $this->routes->add_route('post', 'document-update', [$this, 'update']);
    $this->routes->add_document_save_handler('document', [$this, 'update']);

    $this->routes->add_route('get', 'component-refresh', [$this, 'componentRefresh']);
  }

  public function getDocuments( $data ) {
    return $this->locator->queryPostsForType( isset($data['docType']) ? $data['docType'] : 'content:page', $data );
  }

  public function getAllDocuments( $data ) {
    return $this->locator->queryAllDocumentTypes( $data );
  }

  public function searchDocuments( $data ) {
    return $this->locator->queryAllDocumentTypes( $data );
  }

  public function authorize($check) {
    if ( ! $check ) throw new \Exception( 'Unauthorized');
  }

  public function locate( $params ) {
    return Document::locate( isset($params['id']) ? (int) $params['id'] : null );
  }

  public function create($params) {
    do_action("cs_before_create_request");

    $doc = Document::create( $params['type'] );

    if ( isset( $params['lang'] ) ) $this->wpml->switch_lang( $params['lang'] );


    //some of these updates will return a new object
    //@TODO fix that
    $doc = $doc->update($params);
    $this->authorize($doc->isAllowed('create')); // will ensure the post type is honored for content
    $result = $doc->save();

    if ( isset( $params['lang'] ) ) $this->wpml->switch_back();
    return $result;

  }

  public function read($params) {
    $doc = $this->locate($params);
    if ( ! $doc ) throw new \Exception( 'Not Found');
    $this->authorize($doc->isAllowed());
    return $doc->serialize();
  }

  public function update($params) {
    $doc = $this->locate($params);
    $this->authorize($doc->isAllowed());
    if ( ! $doc->isAllowed() ) unset( $params['title'] );
    return $doc->update( $params )->save();
  }

  public function delete($params) {
    $doc = $this->locate($params);

    if (empty($doc)) {
      throw new \Exception( 'Trying to delete document not found' );
    }

    $this->authorize($doc->isAllowed('delete'));
    return $doc->delete();
  }

  public function componentRefresh() {
    return $this->components->appData();
  }

}
