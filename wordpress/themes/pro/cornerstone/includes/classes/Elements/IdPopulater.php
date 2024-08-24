<?php

namespace Themeco\Cornerstone\Elements;


class IdPopulater {

  protected $count = 0;
  protected $max = 0;
  protected $docRoot = 'e0';
  protected $lastId = 0;

  protected $isPreview = false;
  protected $populated;

  public function walk( $elements, $id ) {

    $element = $elements[$id];
    if (isset($element['_modules'])) {
      $element['_modules'] = array_filter(array_map(function($id) use (&$elements){
        return $this->walk( $elements, $id );
      }, $element['_modules']));
    }

    if ( $this->isPreview ) {
      $element['_id'] = $id;
    } else {

      $numeric_id = (int) substr($id,1) + $this->count;
      $normalized_id = $numeric_id;

      $this->max = max($this->max, $numeric_id);
      $element['_id'] = $normalized_id;

    }

    return $element;
  }

  public function setPreview() {
    $this->isPreview = true;
  }

  public function populate( $elements ) {

    if ( isset( $elements[$this->docRoot] )) { // use the existing ids when doc format is present

      $this->populated = array_filter([$this->walk($elements, $this->docRoot)]);

      $this->lastId = $this->max + 1;
      return $this;
    }

    $regions = $elements;
    if (isset($elements['_type']) && $elements['_type'] === 'root') {
      $regions = isset($elements['_modules']) ? $elements['_modules'] : [];
    }

    $this->populated = array_filter(array_map(function( $input ){

      $region = null;

      if ( ! $this->isPreview ) {
        if (!isset($input['_region'])) {
          trigger_error("Element missing region", E_USER_WARNING);
          return null;
        }
        $region = $input['_region'];
      }

      $mapper = function($element) use ($region, &$mapper) {
        //Root is getting passed?
        if (!is_array($element)) {
          return [];
        }

        if ( $region && ! isset( $element['_region'] ) ) {
          $element['_region'] = $region;
        }

        if ( ! $this->isPreview ) {
          $element['_id'] = $this->count++;
        }

        if ( isset( $element['_modules'] ) ) {
          $element['_modules'] = array_map($mapper, is_array($element['_modules']) ? $element['_modules'] : []);
        }

        return $element;
      };

      return $mapper($input);

    }, $regions));

    $this->lastId = max($this->max, $this->count) + 1;
    return $this;
  }

  public function result() {
    return $this->populated;
  }

  public function combinedResult() {
    return [$this->populated, $this->lastId];
  }

}
