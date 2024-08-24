<?php

namespace Themeco\Cornerstone\Vm\Types;

use Themeco\Cornerstone\Vm\TypedData;
use Themeco\Cornerstone\Vm\Constants;
use Themeco\Cornerstone\Vm\Callstack\Frame;
use Themeco\Cornerstone\Util\Factory;

class TypeResolver {

  private $stack;
  private $frame;

  public function definePrimitive($class) {
    $name = $class::primitive();
    $this->define( $name, new $class( $name ) );
  }

  public function rootSetup() {
    $this->definePrimitive(ScalarString::class);
    $this->definePrimitive(ScalarInt::class);
    $this->definePrimitive(ScalarBool::class);
    $this->definePrimitive(ScalarFloat::class);
    $this->definePrimitive(ObjectAssociative::class);
    $this->definePrimitive(ObjectClassed::class);
    $this->definePrimitive(Any::class);
    return $this;
  }

  public function setup(Frame $frame) {
    $this->frame = $frame;
    return $this;
  }

  public function define( $name, Type $type ) {
    $type->setName( $name );
    $this->frame->set(Constants::Types, $name, $type);
    return $type;
  }

  public function locate( $name ) {
    return $this->frame->get(Constants::Types, $name);
  }

  public function coerce( $input, $type ) {

    if ($type === null) {
      return $input->clone();
    }

    $_type = $this->normalizeType( $type );

    if ($_type->name() === $input->type()) {
      return $input;
    }

    return $this->apply( $input->get(), $_type );
  }


  public function normalizeType( $type ) {
    if ( is_string( $type ) ) {
      return $this->locate( $type );
    }
    return $type;
  }

  public function apply( $input, $type = null) {

    if ( is_a( $input, TypedData::class ) ) {
      return $this->coerce( $input, $type);
    }

    $_type = $type ? $this->normalizeType( $type ) : $this->detect( $input );

    return new TypedData($_type, $input );
  }

  public function detect( $input ) {

    if ( is_a( $input, TypedData::class ) ) {
      return $input->type();
    }

    if ( is_array( $input ) ) {

      if ( ! $this->array_is_list( $input ) ) { // is associative
        return $this->locate( 'object-a' );
      }

      if ( empty($input)) {
        return $this->locateOrCreateArray( $this->locate( 'any' ) );
      }

      $types = array_map(function( $item ) {
        return $this->detect( $item );
      }, $input);

      if ($this->typesAreIdentical($types) ) {
        return $this->locateOrCreateArray($types[0]);
      }

      return $this->locateOrCreateTuple($types);

    }

    if ( is_string( $input ) ) {
      return $this->locate( 'string' );
    }

    if ( is_int( $input ) ) {
      return $this->locate( 'int' );
    }

    if ( is_float( $input ) ) {
      return $this->locate( 'float' );
    }

    if ( is_bool( $input ) ) {
      return $this->locate( 'bool' );
    }

    if ( is_scalar( $input ) ) {
      return $this->locate( 'scalar' );
    }

    if ( !is_null($input) && is_object( $input ) ) {
      $object = $this->detectObject( $input );
      if ($object) {
        return $object;
      }
    }

    return $this->locate( 'any' ); // null values are resolved to "any"
  }

  public function detectObject( $input ) {

    $class = get_class( $input );

    if ( ! $class ) {
      return null;
    }

    $key = "object:$class";
    $located = $this->locate( $key );
    if ( is_null( $located ) ) {
      $located = $this->define( $key, (new ObjectClassed())->setClass($class));
    }

    return $located;
  }

  public function locateOrCreateArray( $type ) {
    $key = $type->name() . '[]';

    $located = $this->locate( $key );
    if ( is_null( $located ) ) {
      $located = $this->define( $key, (new BaseArray())->setType($type));
    }

    return $located;
  }

  public function locateOrCreateTuple( $types ) {
    $typeNames = array_map(function($type) {
      return $type->name();
    }, $types);

    $key = '[' . implode(',', $typeNames) . ']';

    $located = $this->locate( $key );
    if ( is_null( $located ) ) {
      $located = $this->define( $key, (new BaseTuple())->setTypes($types));
    }

    return $located;
  }


  public function array_is_list(array $a) {
    return $a === [] || (array_keys($a) === range(0, count($a) - 1));
  }

  public function typesAreIdentical($types) {

    $first = $types[0]->name();

    for ($i = 1; $i < count($types); $i++) {
      if ($types[$i]->name() !== $first) {
        return false;
      }
    }

    return true;

  }

}
