<?php

namespace Themeco\Cornerstone\Tss;

use Themeco\Cornerstone\Parsy\Util\ParseException;
use Themeco\Cornerstone\Services\DynamicContent;
use Themeco\Cornerstone\Util\Factory;
use Themeco\Cornerstone\Tss\Traits\StackAccessor;
use Themeco\Cornerstone\Tss\Traits\Call;
use Themeco\Cornerstone\Tss\Constants\StatementTypes;
use Themeco\Cornerstone\Tss\Reducers\RuleReducer;
use Themeco\Cornerstone\Tss\Reducers\QueryStyleReducer;
use Themeco\Cornerstone\Tss\Reducers\ModuleReducer;
use Themeco\Cornerstone\Tss\Reducers\DeclarationReducer;
use Themeco\Cornerstone\Tss\Util\IdEncoder;

class Runtime {

  use Call;
  use StackAccessor;

  protected $id = 0;
  protected $env;
  protected $modules = [];
  protected $dynamicContent = [];
  protected $elementCssIds = [];

  protected $ruleReducer;
  protected $dcIdEncoder;
  private $dcService;

  public function __construct(
    RuleReducer $ruleReducer,
    IdEncoder $dcIdEncoder,
    DynamicContent $dcService
  ) {
    $this->ruleReducer = $ruleReducer;
    $this->dcIdEncoder = $dcIdEncoder;
    $this->dcService = $dcService;
  }

  public function setup($id, $env) {
    $this->id = $id;
    $this->env = $env;
    $this->dcIdEncoder->setup( $id, 'dc' );
  }

  public function matchParameterDynamicContent( $value ) {
    preg_match('/{{dc:([A-Za-z0-9_-]*):?((?:[A-Za-z0-9_-]*)(?:.*?))}}/', $value, $matches);
    if ( $matches[1] === 'p' || $matches[1] === 'param' ) {
      return $this->env->getConfig( 'getCssParameterByPath' )( $matches[2] );
    }

    if ( $matches[1] === 'g' || $matches[1] === 'global' ) {
      return $this->env->getConfig( 'getCssGlobalParameterByPath' )( $matches[2] );
    }

    return null;
  }

  public function makeDcVar( $containerScope, $key, $value ) {

    $id = $this->dcIdEncoder->nextId();
    $containerScope->define('dc', $id, $value);
    if ($key === 'css') {
      $this->elementCssIds[] = $id;
    }
    return "var(--tco-$id)";

  }

  public function detectDynamicContentCallback( $containerScope, $key, $match) {
    $param = $this->matchParameterDynamicContent( $match );
    return is_null( $param )
      ? $this->makeDcVar( $containerScope, $key, $match )
      : $this->detectDynamicContent( $containerScope, $key, $param );
  }

  public function detectDynamicContent( $containerScope, $key, $value ) {

    // Ignore all variable creation and just use dynamic content
    if (
      defined("CS_TSS_IGNORE_VARIABLE_CREATION")
      && !empty(\CS_TSS_IGNORE_VARIABLE_CREATION)
    ) {
      $value = \cs_dynamic_content($value);
      return $value;
    }

    // process inner Dynamic Content
    // {{dc:global:{{dc:p:color}}}}
    $value = $this->dcService->processInnerPattern($value);

    // var('{{dc:***}}')
    $stage1 = preg_replace_callback( '/var\(\s*?"({{dc:(?:[A-Za-z0-9_-]*):?(?:[A-Za-z0-9_-]*)(?:.*?)}})"\s*?\)/', function($matches) use ($containerScope, $key){
      return $this->detectDynamicContentCallback( $containerScope, $key, $matches[1]);
    }, $value  );

    // var("{{dc:***}}")
    $stage2 = preg_replace_callback( "/var\(\s*?'({{dc:(?:[A-Za-z0-9_-]*):?(?:[A-Za-z0-9_-]*)(?:.*?)}})'\s*?\)/", function($matches) use ($containerScope, $key){
      return $this->detectDynamicContentCallback( $containerScope, $key, $matches[1]);
    }, $stage1  );

    // {{dc:***}}
    $stage3 = preg_replace_callback( '/{{dc:(?:[A-Za-z0-9_-]*):?(?:[A-Za-z0-9_-]*)(?:.*?)}}/', function($matches) use ($containerScope, $key){
      return $this->detectDynamicContentCallback( $containerScope, $key, $matches[0]);
    }, $stage2  );

    // Render for things like Twig
    $stage3 = apply_filters('cs_tss_post_detect_dynamic_content', $stage3);

    return $stage3;
  }

  public function process( $id, $type, $data, $before = null) {

    $style_id = isset( $data['style_id'] ) ? $data['style_id'] : null;
    $modules = $this->stack->lookup('container', $type);


    // Compile custom user TSS template
    // @see Elements\Definition
    if (!empty($data['tss_template'])) {
      $this->processCustomTSSTemplate($data, $data['tss_template']);
    }

    $containerScope = $this->stack->newScope();

    $containerScope->define('parser', 'valueParser', function($input, $key) use ($containerScope, $style_id) {
      return $this->env->parseValue($input, function($value) use ($containerScope, $key, $style_id) {
        return $this->detectDynamicContent( $containerScope, $key, $value, $style_id );
      });
    });

    $containerScope->define('parser', 'elementCssParser', function($input) use ($containerScope, $style_id) {
      return $this->detectDynamicContent( $containerScope, 'css', $input, $style_id );
    });

    if ( is_callable( $before ) ) {
      $before($data, $containerScope);
    }

    if ( ! is_null( $modules ) ) {
      foreach( $modules as $module ) {
        $this->module( $containerScope, $id, $module, $data );
      }
    }

    $this->dynamicContent[ $id ] = $containerScope->getAllFromNamespace('dc');

  }


  public function normalizeModuleData( $config, $data ) {


    $baseData = is_null( $config['remap'] ) ? $data : $this->remap( $data, $config['remap'] );

    if ( isset($data['_bp_base'] ) ) {
      $bpKey = '_bp_data' . $data['_bp_base'];
      if ( isset( $data[$bpKey] ) ) {
        $baseData['_bp_data'] = is_null( $config['remap'] ) ? $data[$bpKey] : $this->remap( $data[$bpKey], $config['remap'] );
      }
    }

    return $baseData;
  }

  // ID is the name of the calling element
  public function module( $containerScope, $id, $config, $data ) {

    if ( !isset( $this->modules[ $config['module'] ] ) ) {
      $this->modules[ $config['module'] ] = [];
    }

    $baseBreakpoint = $this->stack->lookup('internal', 'baseBreakpoint');
    $ranges = $this->stack->lookup('internal', 'breakpointRanges');
    $size = count($ranges) - 1;

    $modName = $config['name'];

    if ( ! $this->conditionCheck( $data, $config['conditions'] ) ) {
      $this->modules[ $config['module'] ]["$id:$modName"] = [
        $id,
        $modName,
        []
      ];
      return;
    }

    // Fill the array. This ensures the indexes are in the right order before the reduced runs
    $queryResult = [];
    for ($i = 0; $i <= $size; $i++) {
      $queryResult[$i] = [];
    }

    if ( $config['module'] === 'parameters' ) {
      $baseData = ['style_id' => $data['style_id'] ];
      if ( isset( $data['_parameters'] ) && isset( $data['_parameters']['_bp_data'] ) ) {
        $baseData = array_merge( $baseData, $data['_parameters']['_bp_data']);
      }
    } else {
      $baseData = $data;
    }


    $baseData = $this->normalizeModuleData( $config, $baseData );
    $baseResult = $this->runModuleStatements( $containerScope, $config, $baseData, $baseData, true);

    // Base -> UP
    $currentData = $baseData;

    for ($i = $baseBreakpoint + 1; $i <= $size; $i++) {
      $queryResult[$i] = $this->runModuleForBreakpoint($containerScope, $config, $i, $baseData, $currentData);
    }

    // Base -> DOWN
    $currentData = $baseData;

    for ($i = $baseBreakpoint - 1; $i >= 0; $i--) {
      $queryResult[$i] = $this->runModuleForBreakpoint($containerScope, $config, $i, $baseData, $currentData);
    }

    $queryStyleReducer = Factory::create(QueryStyleReducer::class);

    $reduced = $queryStyleReducer->reduce($baseBreakpoint, $baseResult, $queryResult);
    $modName = is_null($config['name']) ? $config['module'] : $config['name'];

    $this->modules[ $config['module'] ]["$id:$modName"] = [ $id, $modName, $reduced ];

  }

  public function runModuleForBreakpoint($containerScope, $config, $i, $baseData, &$currentData) {

    if ( !isset( $baseData['_bp_data'] ) ) {
      return [];
    }

    $indexData = [];

    foreach ($baseData['_bp_data'] as $key => $value ) {
      if ( isset( $value[$i]) && !is_null($value[$i])) {
        $indexData[$key] = $value[$i];
      }
    }

    if (empty($indexData)) {
      return [];
    }

    // At this point remap has already happened
    // At one point it did this twice causing issue #3907
    $currentData = array_merge( // Assign the next layer of data by reference
      $currentData,
      $indexData
    );

    return $this->runModuleStatements( $containerScope, $config, $baseData, $currentData, false);
  }

  public function runModuleStatements( $containerScope, $config, $baseData, $currentData, $isBase ) {
    // 1. Convert $options into call args
    // In try block due to broken usercustom modules
    try {
      list($scope, $statements) = $this->resolveCallable( 'module',  $config['module'], $config['args'], StatementTypes::STYLE, $containerScope);
    } catch (\Exception $e) {
      trigger_error($e->getMessage());
      return [];
    }


    // 2. Parse all "data" into types, then store in the scope
    $scope->define('data', 'module-base', $baseData );
    $scope->define('data', 'module-current', $currentData );
    $scope->define('data', 'is-base', $isBase );



    $scope->processStatements($statements);

    if ( isset( $config['transform'] ) ) {
      // Testing the layout-row bug. It's helpful to add statements in layout-row-columns.tss
      // echo '<pre>';
      // var_dump($statements);
      // echo '<pre>';

      $config['transform']($scope, $baseData, $currentData, $isBase);
    }

    $content = $scope->result()->content();

    return $this->ruleReducer->reduce($content);

  }

  public function remap($data, $remap) {
    $remapped = [];

    foreach ( $data as $key => $value) {
      foreach ($remap as $base => $rewrite) {
        if (strpos($key, $base) === 0) {
          $new_key = $rewrite . substr($key, strlen($base));
          $remapped[$new_key] = $value;
        }
      }
    }


    return $remapped;
  }

  public function conditionCheck( $data, $conditions ) {
    if (empty($conditions)) {
      return true;
    }

    foreach ($conditions as $condition) {
      if (!$data[$condition['key']] === $condition['value']) {
        return false;
      }
    }

    return true;
  }

  public function finalize( $options = [] ) {

    $moduleReducer = Factory::create(ModuleReducer::class);
    $moduleReducer->setup($this->id);
    if ( !empty($options['selectorFormat'] ) ) {
      $moduleReducer->setSelectorFormat($options['selectorFormat']);
    }

    $containers = [];
    $declarations = [];

    $utilized_var_ids = [];
    foreach ( $this->modules as $name => $module ) {
      list($container, $result) = $moduleReducer->reduce($module);

      foreach ( $result as $index => $declaration ) {
        if (preg_match_all('/var\(--tco-([\w-]+)\)/', $result[$index][0], $matches, PREG_SET_ORDER) ) {
          foreach($matches as $match) {
            $utilized_var_ids[$match[1]] = true;
          }
        }
      }

      foreach($container as $id => $modules) {
        if (!isset($containers[$id])) {
          $containers[$id] = [];
        }
        $containers[$id] = array_merge( $containers[$id], $modules );
      }

      $declarations = array_merge( $declarations, $result );
    }

    foreach ( $this->dynamicContent as $id => $list ) {
      if (!isset($containers[$id])) {
        $containers[$id] = [];
      }

      $containers[$id]['dynamic-content'] = [];

      foreach ($list as $key => $value) {
        if (isset( $utilized_var_ids[$key] ) || in_array( $key, $this->elementCssIds ) ) {
          $containers[$id]['dynamic-content'][$key] = $value;
        }
      }

    }

    $tss = DeclarationReducer::reduce(
      $declarations,
      $this->stack->lookup('internal', 'baseBreakpoint'),
      $this->stack->lookup('internal', 'breakpointRanges'),
      $this->stack->lookup('internal', 'selectorPrefix')
    );

    return [
      'containers' => $containers,
      'tss' => $tss
    ];
  }

  /**
   * Renders a custom user TSS
   * Does several Shims for Pro4-5 support
   * Adds data variables to stack
   * Adds module "usercustom"
   *
   * @param array $data Element data
   * @param string $tss
   */
  private function processCustomTSSTemplate($data = [], $tss = '') {
    // Shim for Pro4 elements
    $tss = str_replace('.$_el', "&", $tss);
    $tss = str_replace('!==', "!=", $tss);
    $tss = str_replace('===', "==", $tss);

    // Unless support
    $tss = str_replace('@unless', "@if not", $tss);
    $tss = str_replace('||', "or", $tss);
    // $variable?? now translates to "not empty($variable)"
    $tss = preg_replace('/(\$\w*)\?\?/i', 'empty($1)', $tss);

    $toCompile = $tss;

    // Compile from Compiler
    Factory::setup();
    $compiler = Factory::create(Compiler::class);
    $compiler->setup();

    try {
      // Shim as Pro4 called variables directly
      foreach ($data as $key => $value) {
        $typeData = $this->stack->evaluator()->resolve($value);
        $typeData->setValue(cornerstone_post_process_value($typeData->value()));
        $this->stack->define("variable", $key, $typeData);
      }

      // Get statements to add to usercustom
      $customMod = $compiler->getParserStatement($toCompile);
      $this->stack->define("module", "usercustom", [[], $customMod]);

    } catch(ParseException $e) {
      // Debug parse error
      if (WP_DEBUG) {
        echo $e->getNiceMessage();
      }

      // Define null module so it doesn't have a fatal error
      $this->stack->define("module", "usercustom", [[], []]);
    } catch (\Throwable $e) {
      if (WP_DEBUG) {
        echo $e->getMessage();
      }
    }
  }
}
