<?php

namespace Themeco\Cornerstone\Util;

class IocContainer {

  protected $container = [];
  protected $cache = [];
  protected $registrationHandler;

  public function register($class, $instance) {
    $this->container[$class] = $instance;
  }

  public function setRegistrationHandler($cb) {
    $this->registrationHandler = $cb;
  }

  public function getReflector( $class ) {

    if ( ! isset( $this->cache[$class] ) ) {
      $reflector = new \ReflectionClass($class);
      $constructor = $reflector->getConstructor();
      $this->cache[$class] = [
        $reflector->getInterfaces(),
        $constructor ? $this->mapConstructorParams( $constructor->getParameters() ) : null,
        $reflector->hasMethod('setup')
      ];
    }

    return $this->cache[$class];

  }

  public function mapConstructorParams( $params ) {
    return array_map( function( $param ) {

      // Check type and if valid parameter
      // Common thing to check hasType i guess
      // https://github.com/taoso/phpcd.vim/commit/b41796f64f72cde4727839f253dc102dfa4848e1
      if (
        empty($param)
        || ! method_exists($param, 'hasType')
        || ! $param->hasType()
      ) {
        return null;
      }

      $type = $param->getType();

      if ( $type->isBuiltin() ) {
        return null;
      }

      if ( PHP_VERSION_ID > 71000 )  {
        return (string) $type;
      }

      if (method_exists($type, 'getName')) {
        return $type->getName();
      }

      return null;
    }, $params );
  }

  public function makeInstance( $class, $constructorParameters ) {

    $parameters = array_map(function( $className ) {
      return $className ? $this->resolve( $className ) : null;
    }, $constructorParameters);

    return new $class(...$parameters);

  }

  public function resolve($class) {

    if ( isset( $this->container[$class] ) ) return $this->container[$class];

    if ( ! class_exists( $class ) ) {
      throw new \Exception("Class $class not found");
    }

    list($interfaces, $constructorParameters, $hasSetup) = $this->getReflector($class);
    $instance = $constructorParameters ? $this->makeInstance( $class, $constructorParameters ) : new $class();

    $cb = $this->registrationHandler;

    if ( is_callable( $cb ) ) {
      $cb( $class, $instance, $interfaces, $hasSetup );
    }

    return $instance;
  }

}
