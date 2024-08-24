<?php

namespace Themeco\Cornerstone\Elements;

class ComponentMap {

  public $result = [];
  public $elements = [];

  public function setElements($elements) {
    $this->elements = $elements;
    return $this;
  }

  public function run() {
    $this->map("e0");
    return $this->result;
  }

  public function map( $id ) {
    if ( $this->isComponentExport( $id ) ) {
      $this->result[trim($this->elements[$id]['_c_id'])] = [
        'id' => $id,
        'slots' => $this->identifySlots( $id ),
        'thru' => $this->identifyPassThrough( $id )
      ];
    }
    $children = isset( $this->elements[$id]['_modules'] ) ? $this->elements[$id]['_modules'] : [];
    foreach ($children as $child) {
      $this->map($child);
    }
  }

  public function flaggedExport( $id ) {
    return isset( $this->elements[$id] )
      && isset( $this->elements[$id]['_c_export'] )
      && isset( $this->elements[$id]['_c_id'] )
      && $this->elements[$id]['_c_export']
      && $this->elements[$id]['_c_id'];
  }

  public function flaggedSlot( $id ) {
    return isset( $this->elements[$id] )
      && isset( $this->elements[$id]['_c_slot'] )
      && isset( $this->elements[$id]['_c_id'] )
      && $this->elements[$id]['_c_slot']
      && $this->elements[$id]['_c_id'];
  }

  public function closestExport( $id ) {
    $next = $id;
    while ( isset($this->elements[$next]['_parent']) && $this->elements[$next]['_parent'] ) {
      $next = $this->elements[$next]['_parent'];
      if ( $this->flaggedExport( $next ) ) return $next;
    }
    return null;
  }

  public function closestSlot( $id ) {
    $next = $id;
    while ( isset($this->elements[$next]['_parent']) && $this->elements[$next]['_parent'] ) {
      $next = $this->elements[$next]['_parent'];
      if ( $this->flaggedSlot( $next ) ) return $next;
    }
    return null;
  }

  public function isComponentExport($id) {

    if ( ! $this->flaggedExport( $id ) ) return false;

    // Can't be inside another component
    if ($this->closestExport( $id )) false;

    // Can't have components as descendants
    if ($this->findDescendantExport( $id )) false;

    // label required
    if ( ! $this->elements[$id]['_label'] ) return false;

    return true;
  }

  public function isComponentSlot($id) {

    if (! $this->flaggedSlot($id) ) return false;

    // Must be a inside a component OR a component itself
    if ( ! $this->flaggedExport( $id ) && ! $this->closestExport( $id ) ) return false;


    // Can't be inside another slot
    if ( $this->closestSlot( $id ) ) return false;

    // Can't have slots as descendants
    if ( $this->findDescendantSlot( $id ) ) return false;

    // label required
    if ( ! $this->elements[$id]['_label'] ) return false;

    return true;
  }

  public function isPassThrough( $id ) {
    return isset( $this->elements[$id] )
      && isset( $this->elements[$id]['_c_id'] )
      && isset( $this->elements[$id]['_c_thru'] )
      && $this->elements[$id]['_c_id']
      && $this->elements[$id]['_type'] !== 'slot'
      && !!$this->elements[$id]['_c_thru'];
  }



  public function findDescendantExport( $target ) {
    $seek = function( $id ) use (&$seek, $target){
      if ( $this->flaggedExport( $id) && $id !== $target ) {
        return $id;
      }
      $children = isset( $this->elements[$id]['_modules'] ) ? $this->elements[$id]['_modules'] : [];
      foreach ( $children as $child ) {
        $found = $seek($child);
        if ($found) return $found;
      }
      return null;
    };
    return $seek($target);
  }

  public function findDescendantSlot( $target ) {
    $seek = function( $id ) use (&$seek, $target){
      if ( $this->flaggedSlot( $id) && $id !== $target ) {
        return $id;
      }
      $children = isset( $this->elements[$id]['_modules'] ) ? $this->elements[$id]['_modules'] : [];
      foreach ( $children as $child ) {
        $found = $seek($child);
        if ($found) return $found;
      }
      return null;
    };
    return $seek($target);
  }

  public function identifySlots( $id ) {

    if ( $this->isComponentSlot( $id ) ) {
      return [[$id, trim($this->elements[$id]['_c_id'])]];
    }

    $slots = [];

    $walk = function($id) use (&$walk, &$slots) {
      if ( $this->isComponentSlot( $id ) ) {
        $slots[] = [$id, trim($this->elements[$id]['_c_id'])];
        return;
      }
      $children = isset( $this->elements[$id]['_modules'] ) ? $this->elements[$id]['_modules'] : [];

      foreach ($children as $child) {
        $walk($child);
      }
    };

    $walk($id);


    return $slots;

  }

  public function identifyPassThrough( $id ) {

    if ( $this->isPassThrough( $id ) ) {
      return [[$id, trim($this->elements[$id]['_c_id'])]];
    }

    $thru = [];

    $walk = function($id) use (&$walk, &$thru) {
      if ( $this->isPassThrough( $id ) ) {
        $thru[] = [$id, trim($this->elements[$id]['_c_id'])];
        return;
      }
      $children = isset( $this->elements[$id]['_modules'] ) ? $this->elements[$id]['_modules'] : [];
      foreach ($children as $child) {
        $walk($child);
      }
    };

    $walk($id);
    return $thru;

  }
}
