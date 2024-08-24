<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Util\Parameter;
use Themeco\Cornerstone\Plugin;
use Themeco\Cornerstone\Vm\Constants;
use Themeco\Cornerstone\Vm\Runtime;
use Themeco\Cornerstone\Tss\Util\IdEncoder;

class Vm implements Service {

  protected $idEncoders = [];

  protected $plugin;
  protected $ctx;
  protected $themeOptions;
  protected $codebaseBridge;


  public function __construct(Plugin $plugin, Runtime $ctx, ThemeOptions $themeOptions, CodebaseBridge $codebaseBridge) {
    $this->plugin = $plugin;
    $this->ctx = $ctx;
    $this->themeOptions = $themeOptions;
    $this->codebaseBridge = $codebaseBridge;
  }

  public function setup() {
    $this->ctx->setup();
    $this->codebaseBridge->legacyPlugin()->component('Looper_Manager');
    add_action( 'after_setup_theme', [ $this, 'setupGlobals' ] );
  }

  public function setupGlobals() {
    $json = $this->themeOptions->get_value('cs_global_parameter_json');
    $data = $this->themeOptions->get_value('cs_global_parameter_data');
    $parameters = Parameter::create( null, null, $json ? $json : '' )->apply( ! empty ( $data ) ? $data : [] );
    Parameter::defineParametersForRender($parameters, $this->ctx->stack()->frame(), 'global');
  }

  public function runtime() {
    return $this->ctx;
  }

  public function getParameterByPath( $key ) {
    return $this->getParameterByPathFromStack($this->ctx->stack()->active(), $key);
  }

  public function getGlobalParameterByPath( $key ) {
    return $this->getParameterByPathFromStack($this->ctx->stack()->frame(), $key);
  }


  public function getCssParameterByPath( $key ) {
    return $this->getCssParameterByPathFromStack($this->ctx->stack()->active(), $key);
  }


  public function getCssGlobalParameterByPath( $key ) {
    return $this->getCssParameterByPathFromStack($this->ctx->stack()->frame(), $key);
  }

  public function getCssParameterByPathFromStack( $stack, $key ) {

    // 1. If the path is NOT for CSS, return null
    // 2. If the path is NOT responsive, return the value directly
    // 3. If the path IS responsive, return a var ID;

    $id = $stack->get(Constants::ParameterCss, $key );

    if ($id) {
      // Generate param ID
      $encoder = IdEncoder::getEncoder("param",$id);
      return 'var(--' . $encoder->idForPath( $key ) .')';
    }

    return null;

  }

  public function getParameterByPathFromStack( $stack, $key ) {
    $path = '';

    if (($dotIndex = strpos($key, '.')) !== false) {
      $path = substr($key, $dotIndex + 1);
      $key = substr($key, 0, $dotIndex);
    }

    return cornerstone('DynamicContent')->postProcessValue(
      cs_dynamic_content( $this->resolveParam( $stack, $key, $path ) )
    );

  }

  public function resolveParam( $stack, $key, $path ) {

    $value = $stack->get(Constants::Parameter, $key );

    return $path ? cs_get_path( $value, $path ) : $value;
  }

}
