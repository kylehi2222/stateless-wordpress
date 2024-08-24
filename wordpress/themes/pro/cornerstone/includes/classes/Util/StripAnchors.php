<?php

namespace Themeco\Cornerstone\Util;

class StripAnchors {

  protected $stacks = [];
  protected $linkStack = [];

  public function setup() {
    add_filter( 'cs_in_link', [ $this, 'isInLink' ]);
  }

  public function isInLink() {
    return count( $this->linkStack ) > 0;
  }

  public function maybeAddLink( $parent, $elementService ) {

    if ( empty( $parent ) || ! isset(  $parent['_type']) || ! isset( $parent['_id']) ) {
      return function() {};
    }

    $definition = $elementService->get_element( $parent['_type'] );
    $isAnchor = $definition->will_render_link( $parent );
    $isOffscreenDropzone = $definition->has_offscreen_dropzone();

    if ($isOffscreenDropzone) {
      // start a new context
      array_push($this->stacks, $this->linkStack);
      $this->linkStack = [];
    } else if ($isAnchor) {
      // in the current context, we will be in a link until this parent stops rendering
      array_push($this->linkStack, $parent['_id']);
    }

    return function() use ($isAnchor, $isOffscreenDropzone) {
      if ($isOffscreenDropzone) {
        // return to previous context when leaving the dropzone
        $this->linkStack = array_pop( $this->stacks );
      } else if ($isAnchor) {
        // remove current ID after rendering children
        array_pop( $this->linkStack );
      }
    };
  }

  public function clean($html) {

    if ( $this->isInLink() ) {
      return preg_replace_callback('/<a[\s]+([^>]+)>((?:.(?!\<\/a\>))*.)<\/a>/', [ $this, 'cleanCb'], $html );
    }

    return $html;

  }

  public function cleanCb( $matches ) {

    $atts = trim(preg_replace_callback('/(\w*) *= *(([\'"])?((\\\3|[^\3])*?)\3|(\w+))/', [$this, 'cleanAttsCb'], $matches[1]));
    return "<span $atts>" . $matches[2] . '</span>';

  }

  public function cleanAttsCb( $matches ) {
    return in_array( $matches[1], [ 'href', 'target', 'download', 'ping', 'rel', 'hreflang', 'type', 'referrerpolicy']) ? '' : $matches[0];
  }

}