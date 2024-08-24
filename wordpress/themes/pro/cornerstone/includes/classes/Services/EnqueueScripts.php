<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Plugin;
use Themeco\Cornerstone\Util\ErrorHandler;
use Themeco\Cornerstone\Util\MinifyCss;
use Themeco\Cornerstone\Util\JsAsset;

class EnqueueScripts implements Service {

  protected $inlineScripts = [];
  protected $count = 0;
  protected $docs = [];
  protected $components = [];
  protected $plugin;
  protected $themeManagement;
  protected $themeOptions;
  protected $jsAsset;

  public function __construct(Plugin $plugin, ThemeManagement $themeManagement, ThemeOptions $themeOptions, JsAsset $jsAsset) {
    $this->plugin = $plugin;
    $this->themeManagement = $themeManagement;
    $this->themeOptions = $themeOptions;
    $this->jsAsset = $jsAsset;
  }

  public function addCustomJsFromDocument( $document ) {
    $id = $document->id();
    if ( ! in_array( $id, $this->docs, true ) ) {
      $this->addScriptSafely( 'cs-' . $id, $document->getCustomJs() );
      $this->addScriptsForReferencedComponents($document->getReferencedComponents());
      $this->docs[] = $id;
    }
  }

  public function addScriptsForReferencedComponents( $ids ) {

    if ( count( $ids ) < 0 ) {
      return;
    }

    list($data, $componentIncludes) = $this->plugin->service('Components')->enumerate();

    foreach ($ids as $id ) {
      if ( ! in_array( $id, $this->components, true ) && ! in_array( $id, $this->docs, true ) ) {
        $key = 'c' . $id;
        if (isset( $componentIncludes[$key][1])) {
          $this->addScriptSafely( 'cs-' . $id, $componentIncludes[$key][1] );
        }
        $this->components[] = $id;
      }
    }

  }

  public function makeCustomJsTag( $script ) {
    return wp_get_inline_script_tag( $script );
  }

  public function setup() {
    add_action('wp_enqueue_scripts', [$this, 'enqueue'], 5);
    add_action('cs_element_rendering', [$this, 'register']);
    add_action('wp_footer', [$this, 'globalCustomJs'], 0);
    add_action('wp_footer', [$this, 'outputInlineScripts'], 9998);
  }

  public function enqueue() {
    $this->register();
    wp_enqueue_script( 'cs' );

    // JQuery Add in removal
    // @TODO Remove when the plugins actually have removed jquery
    if (apply_filters("cs_use_jquery_everywhere", true)) {
      wp_enqueue_script( 'jquery' );
    }
  }

  public function register() {
    $is_classic = (
      $this->themeManagement->isClassicElementsEnabled()
      || $this->themeManagement->isClassic()
    );

		$script_asset = $this->jsAsset->get( $is_classic ? "assets/js/site/cs-classic" : "assets/js/site/cs" );

    wp_register_script( 'cs', $script_asset['url'], [], $script_asset['version'], true );

    // Link selector for links.js
    $linkSelector = $this->themeManagement->compatibilityMode() || cs_stack_is_custom()
      ? 'a[href*="#"]'
      : '#x-root a[href*="#"]';

    // CS JS data
    // breakpoints and link selector currently
    $csJsData = [
      'linkSelector' => apply_filters( 'cs_link_selector', $linkSelector),
      'bp' => cs_breakpoint_config(),
    ];
    $csJsData = apply_filters("cs_js_data", $csJsData);
    wp_localize_script( 'cs', 'csJsData', $csJsData);

    // Lottie registry
    $lottie_script = $this->jsAsset->get( 'assets/js/site/cs-lottie' );
    wp_register_script( 'cs-lottie', $lottie_script['url'], [ 'cs' ], $lottie_script['version'], true );

    // Accordion registry
    // @TODO dynamic way of this
    $accordion_script = $this->jsAsset->get( 'assets/js/site/cs-accordion' );
    wp_register_script( 'cs-accordion', $accordion_script['url'], [ 'cs' ], $accordion_script['version'], true );


  	wp_register_script( 'cs-ilightbox', $this->plugin->url . '/assets/js/site/ilightbox.js', [ 'cs', 'jquery' ], CS_VERSION, true );
		wp_register_script( 'cs-flexslider', $this->plugin->url . '/assets/js/site/flexslider.js', [ 'cs', 'jquery' ], CS_VERSION, true );
  }

  public function globalCustomJs() {
    $this->addScriptSafely('cornerstone-custom-js', $this->themeOptions->get_global_js() );
  }

  public function addScript($id, $content, $type = 'text/javascript', $no_check = false ) {
    if ( $no_check || ! $this->scriptIsEmpty( $content ) ) {
      $this->inlineScripts[ $id ] = [ $content, $type ];
    }
  }

  public function addScriptSafely($id, $content, $type = 'text/javascript' ) {
    // Apply DC to JS
    if (apply_filters('cs_custom_js_dynamic_content', true)) {
      $content = cs_dynamic_content($content);
    }

    if ( ! $this->scriptIsEmpty( $content ) ) {
      $this->addScript( $id, $this->protectScript( $content, $id ), $type, true );
    }
  }

  public function removeScript( $id ) {
    unset($this->inlineScripts[ $id ]);
  }

  public function getInlineScripts() {

    $output = '';
    foreach ($this->inlineScripts as $id => $script) {
      $output .= wp_get_inline_script_tag( $script[0], [ 'id' => $id, 'type' => $script[1]] );
    }

    return $output;

  }

  public function outputInlineScripts() {
    echo $this->getInlineScripts();
  }

  public function scriptIsEmpty( $content ) {
    $pattern = '/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\'|\")\/\/.*))/';
    $output = preg_replace($pattern, '', $content);
    return ! trim( $output );
  }

  public function outputScript( $content, $safe = true, $type = 'text/javascript' ) {

    if ( ! $this->scriptIsEmpty( $content ) ) {
      if ( $safe ) {
        $content = $this->protectScript( $content );
      }
      echo wp_get_inline_script_tag( $content, [ 'type' => $type ]);
    }

  }

  public function protectScript( $content, $handle = '', $wrapInTry = false ) {
    if ($wrapInTry) {
      return "try { $content } catch( e ) { console.warn('Inline script $handle failed to run', e) }";
    }

    return $content;
  }

  public function getJsAsset($url = "") {
    return $this->jsAsset->get($url);
  }
}
