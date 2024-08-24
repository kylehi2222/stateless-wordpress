<?php

namespace Themeco\Cornerstone\Services;

// use cs_expand_content anywhere you would normally use do_shortcode but also want dynamic content to expand
// use dynamic content anywhere you know is already inside do_shortcode or apply_filters('the_content')


class DynamicContent implements Service {

  const PATTERN = "/{{dc:([\w.-]*):?([\w.-]*)(.*?)}}/";

  // Pattern to match that there is no
  // inner dynamic like {{dc:post:{{dc:post:name}}}}
  const HAS_INNER_CONTENT = "/{{[^}]*{dc:.*}}/";
  const INNER_PATTERN = "/{{[^{]*?((?!{{.*}}))}}/";

  const MAX_DEPTH = 100;

  const FILTER_PREFIX = "cs_dynamic_content_";

  protected $fields = array();
  protected $groups = array();
  protected $dynamicOptions = [];
  protected $cache = array();
  protected $recursion_depth = 0;
  protected $max_recursion_depth;

  protected $vm;

  public function __construct(Vm $vm) {
    $this->vm = $vm;
  }
  public function setup() {
    add_action( 'init', [ $this, 'init' ] );
    $this->max_recursion_depth = apply_filters( 'cs_dynamic_content_max_recursion_depth', 1000 );
  }

  public function init() {

    CS()->component( 'Dynamic_Content_Post' );
    CS()->component( 'Dynamic_Content_Term' );
    CS()->component( 'Dynamic_Content_User' );
    CS()->component( 'Dynamic_Content_Query' );
    CS()->component( 'Dynamic_Content_Looper' );
    CS()->component( 'Dynamic_Content_Url' );
    CS()->component( 'Dynamic_Content_Rivet' );

    do_action( 'cs_dynamic_content_register' );

    CS()->component( 'Dynamic_Content_Global' );

    // Dynamic Content
    add_filter( 'cs_decode_shortcode_attribute', 'cs_dynamic_content_string' );
    add_filter( 'get_the_excerpt',  'cs_dynamic_content_string', 100 );
    add_filter( 'cs_css_post_process', 'cs_dynamic_content_string' );
    add_filter( 'widget_text', 'cs_dynamic_content_string', 0 ); // hopefully before do_shortcode

    // Setup cs_expand_content function
		add_filter( 'cs_expand_content', 'cs_dynamic_content_string' ); // First run dynamic content

    // removing this and only running inside the text element for now
    add_filter( 'cs_expand_content', 'wp_filter_content_tags' ); // run applicable WordPress filters (only wp_filter_content_tags for now)
    add_filter( 'cs_expand_content', 'do_shortcode', 11 ); // run do_shortcode on the final result
    add_filter( 'cs_defer_html', 'cs_expand_content' );


    // Parameters / Globals

    add_filter('cs_dynamic_content_param', [$this, 'supplyFieldParam' ], 10, 3 );
    add_filter('cs_dynamic_content_p', [$this, 'supplyFieldParam' ], 10, 3 );

    add_filter('cs_dynamic_content_g', [$this, 'supplyGlobalFieldParam' ], 10, 3 );

    add_action('cs_dynamic_content_setup', [$this, 'setupParameters'], 20);
  }

  public function setupParameters() {
    cornerstone_dynamic_content_register_group(array(
      'name'  => 'param',
      'aliases' => ['p'],
      'label' => csi18n('app.dc.group-title-parameters')
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'element',
      'group' => 'param',
      'label' => csi18n( 'app.dc.parameters.element' ),
      'controls' => array( array(
        'key'     => 'param',
        'type'    => 'text',
        'label'   => csi18n('app.dc.name')
      ) ),
      'deep' => true,
      'twig_format' => '{{ param.$param }}',
      'format' => '{{dc:param:$param}}'
    ));
  }

  public function postProcessValue( $input ) {
    // String
    if (is_string( $input)) {
      // Global family, weight, and color process
      return preg_replace_callback('/global-((?:ff|fw|color)):([|:.\w-]+)/', function($matches) {
        switch ($matches[1]) {
        case 'color': return cornerstone('GlobalColors')->cssPostProcessColor( $matches[0] );
        case 'ff':    return cornerstone('GlobalFonts')->cssPostProcessFontFamily($matches[2]);
        case 'fw':    return cornerstone('GlobalFonts')->cssPostProcessFontWeight($matches[2]);
        default: return '';
        }
      }, $input );
    }

    // Preprocess all keys if array
    if (is_array($input)) {
      foreach ($input as $key => $value) {
        $input[$key] = $this->postProcessValue($value);
      }
    }

    // Fallback
    return $input;
  }

  /**
    * Paramter output
   * {{dc:p}}
   */
  public function supplyFieldParam( $result, $field, $args ) {
    // If in global custom css
    if (apply_filters("cs_dynamic_content_global_parameter_context", false)) {
      return $this->vm->getCssGlobalParameterByPath($field);
    }

    return $this->postProcessValue( $this->vm->getParameterByPath($field) );
  }

  /**
   * Parameter output
   * {{dc:g}}
   */
  public function supplyGlobalFieldParam( $result, $field, $args ) {
    return $this->postProcessValue( $this->vm->getGlobalParameterByPath($field) );
  }

  /**
   * Main processing to output
   */
  public function run( $content, $asString = true ) {
    // Processes inner pattern
    $content = $this->processInnerPattern($content);

    if ( ! $asString ) {
      list( $result, $args ) = $this->expand( $this->find_matches( $content ) );
      return $result;
    }

    $content = apply_filters( 'cs_dynamic_content_before_expand_string', $content );

    if ( ! is_string( $content ) ) {
      return $content;
    }

    // for {{dc}}
    $content = $this->renderString($content);
    $content = apply_filters('cs_dynamic_content_after_render', $content);
    return $content;
  }

  /**
   * For {{dc:post:{{dc:post:null fallback="title"}}}}
   * This makes sure {{dc:post:null}} gets processed first
   * And returns and then sends to normal processing
   */
  public function processInnerPattern($content, $depth = 0) {
    // Type safety
    if ( ! is_string( $content ) ) {
      return $content;
    }

    preg_match(static::HAS_INNER_CONTENT, $content, $matches);

    // No match standard
    if (empty($matches)) {
      return $content;
    }

    // Max depth reached
    if ($depth > static::MAX_DEPTH) {
      trigger_error("Max depth reached for inner dynamic content processing : Depth " . static::MAX_DEPTH);
      return $content;
    }

    // Inner {{dc:post:title}} with NO inner dynamic content
    $inner = preg_replace_callback(static::INNER_PATTERN, function($match) {
      // Internal process to full string
      return $this->renderString($match[0]);
    }, $content);

    // Process till no inner patterns found
    return $this->processInnerPattern($inner, ++$depth);
  }

  /**
   * Raw template to string output
   */
  private function renderString($content) {
    // for {{dc}}
    $content = preg_replace_callback(
      static::PATTERN,
      array( $this, 'expand_string_callback' ),
      $content
    );

    // For encoded version (e.g. Subject, Telephone)
    return preg_replace_callback(
      '/%7B%7Bdc:([\w.-]*):?([\w.-]*)(.*?)%7D%7D/',
      array( $this, 'expand_string_callback_encoded' ),
      $content
    );
  }


  public function register_field($field) {

    if (isset($field['name']) && isset($field['group']) ) {
      $key = $field['group'] . ':' . $field['name'];
      $this->fields[$key] = $field;
    }

    // Auto setup filter
    if (isset($field['filter']) && is_callable($field['filter'])) {
      $key = static::FILTER_PREFIX . $field['group'] . '_' . $field['name'];
      add_filter($key, $field['filter'], 10, 2);
    }
  }

  public function register_group( $group ) {

    if (isset($group['name']) ) {
      $this->groups[$group['name']] = cs_define_defaults( $group, array(
        'label' => 'Group',
        'aliases' => [],
      ) );
    }

  }

  public function get_dynamic_fields() {

    do_action( 'cs_dynamic_content_setup' );

    return apply_filters( 'cs_dynamic_content_ui', array(
      'fields' => $this->fields,
      'groups' => $this->groups
    ) );

  }

  public function find_matches( $content ) {
    preg_match(static::PATTERN, trim( $content ), $matches );
    return $matches;
  }

  public function parse_string( $content ) {
    return $this->parse_matches( $this->find_matches( $content ) );
  }

  public function expand_string_callback_encoded( $matches ){
    return $this->expand_string_callback( $matches, true );
  }

  public function parse_matches( $matches ) {
    $type  = isset($matches[1]) ? $matches[1] : '';
    $field = isset($matches[2]) ? $matches[2] : '';
    $args  = isset($matches[3]) ? $this->parse_args( trim($matches[3]) ) : array();
    return [ $type, $field, $args ];
  }

  public function expand( $matches ) {

    list( $type, $field, $args ) = $this->parse_matches( $matches );

    $result = apply_filters( static::FILTER_PREFIX . "{$type}", '', $field, $args );
    $result = apply_filters( static::FILTER_PREFIX . "{$type}_{$field}", $result, $args );

    return [ $result, $args ];
  }

  public function expand_string_callback( $matches, $encode_uri = false ) {

    list( $result, $args ) = $this->expand( $matches );

    if ( ! $result && $result !== '0' && isset( $args['fallback'] ) ) {
      // DC Bandaid usage of fallback as dc
      if (strpos($args['fallback'], 'dc:') === 0) {
        $args['fallback'] = "{{" . $args['fallback'] . "}}";
      }

      $result = $args['fallback'];
    }

    // if URL must be encoded
    if ( $encode_uri ) {
      $result = rawurlencode( $result );
    }

    if ( isset( $args['type'] ) ) {
      switch ($args['type']) {
        case 'json':
          // pretty_print JSON arg
          $prettyPrint = cs_get_array_value($args, 'pretty_print', false)
            ? JSON_PRETTY_PRINT
            : 0;

          $result = json_encode( $result, $prettyPrint );
          break;
        case 'date':
          // Format to use or default
          $format = !empty( $args['format'] ) ? $args['format'] : get_option('date_format');

          // if already numeric allow to go through date_i18n
          // as a timestamp will not work here
          if (is_string($result)) {
            $result = strtotime( $result );
          }

          $result = date_i18n( $format, $result);
          break;
        case 'image':
          $result = \cs_resolve_image_source($result);
          break;
        case 'image-css-url':
          $result = 'url(' . \cs_resolve_image_source($result) . ')';
          break;
        case 'file':
          $local = (bool)cs_get_array_value($args, 'local', false);
          $result = \cs_resolve_attachment_source($result, $local);
          break;
        case 'time':
          $result = date_i18n( isset( $args['format'] ) ? $args['format'] : get_option('time_format'), strtotime( $result ) );
          break;
        case 'object':
          // Grab by key
          if (( isset($args['key']) || isset($args['_key']) ) && is_array($result)) {
            // For using with meta and other dc
            // that uses `key`, but cannot support
            // dot syntax
            $key = !isset($args['_key'])
              ? $args['key']
              : $args['_key'];

            // Grab path from values
            $result = cs_get_path($result, $key);
          }
          break;
      }
    }

    if ( is_scalar( $result ) && ! is_string( $result ) ) {
      $result = (string) $result;
    }

    if ( is_array( $result ) ) {
      $strings = array_filter( $result, 'is_scalar' );
      $result = implode( isset( $args['delimiter'] ) ? $args['delimiter'] : ',', $strings );
    }

    if ( is_string( $result ) ) {
      $result = apply_filters( 'cs_dynamic_content_string_result', $result, $args );

      $this->recursion_depth++;
      if ($this->recursion_depth > $this->max_recursion_depth) {
        trigger_error( 'Dynamic Content stopped proccessing after a depth of 100. It is likely that you have self referencing dynamic content', E_USER_WARNING );
        return $result;
      }

      $result = cs_expand_content( $result );
      $this->recursion_depth--;

    }

    // Null type explictly to empty string
    if (is_null($result)) {
      return '';
    }

    // Expected return is string
    // and something has gone wrong
    if (!is_scalar($result)) {
      if (WP_DEBUG) {
        trigger_error(
          'Dynamic Content return value not scalar : ' . gettype($result),
          E_USER_WARNING
        );
      }

      return "";
    }

    return (string)$result;

  }



  public function parse_args( $arg_string ) {

    $args = array();


    // un wptexturize
    $arg_string = str_replace( '&#8220;', '"', $arg_string ); // opening curly double quote
    $arg_string = str_replace( '&#8221;', '"', $arg_string ); // closing curly double quote
    $arg_string = str_replace( '&#8217;', "'", $arg_string ); // apostrophe
    $arg_string = str_replace( '&#8242;', "'", $arg_string ); // prime
    $arg_string = str_replace( '&#8243;', '"', $arg_string ); // double prime
    $arg_string = str_replace( '&#8216;', "'", $arg_string ); // opening curly single quote
    $arg_string = str_replace( '&#8217;', "'", $arg_string ); // closing curly single quote
    $arg_string = str_replace( '\\"',     '"', $arg_string ); // double escaped string (like a json escape)

    $arg_string = str_replace('&amp;quot;', '&quot;', $arg_string );
    $arg_string = str_replace('&amp;apos;', '&apos;', $arg_string );
    $arg_string = preg_replace_callback( '%(?:[^\\\](?:(&quot;)|(&apos;)))%', array( $this, 'parse_args_normalize_quotes' ), $arg_string);
    $arg_string = preg_replace_callback( '%(?:[^\\\](?:(&quot;)|(&apos;)))%', array( $this, 'parse_args_normalize_quotes' ), $arg_string);

    preg_match_all( '%(\w+)\s*=\s*(?:"((?:[^"\\\]++|\\\.)*+)"|\'((?:[^\'\\\]++|\\\.)*+)\')%', $arg_string, $matches );

    foreach( $matches[1] as $index => $key ) {
      $arg = '';
      if ( isset( $matches[2][$index] ) && static::isValidValue($matches[2][$index])) $arg = $matches[2][$index];
      if ( isset( $matches[3][$index] ) && static::isValidValue($matches[3][$index])) $arg = $matches[3][$index];
      $args[$key] = str_replace( '\}', '}', wp_unslash( $arg ) );
    }

    return $args;
  }

  // Allow '0' to be a valid value
  public static function isValidValue($value) {
    return $value || $value === "0";
  }

  public function parse_args_normalize_quotes($matches) {
    return str_replace('&apos;', "'", str_replace('&quot;', '"', $matches[0]));
  }

  public function get_contextual_post( $args = [] ) {
    // see Cornerstone_Dynamic_Content_Post::supply_field
    global $post;

    return $this->get_cached_post(
      isset( $args['post'] ) ? $args['post'] : apply_filters( 'cs_dynamic_content_post_id', is_a( $post, 'WP_Post' ) ? $post->ID : get_the_ID() )
    );
  }

  public function get_cached_post( $post_id ) {


    $key = "post:" . (in_array( $post_id, ['prev', 'next', 'parent' ]) ? get_the_ID() . ':' . $post_id : $post_id);

    if ( ! isset( $this->cache[$key] ) ) {

      $post = null;
      $current_id = get_the_ID();

      if ( 'next' === $post_id ) {
        $next = get_next_post();
        if ( is_a( $next, 'WP_Post' ) && $current_id !== $next->ID ) {
          $post = $next;
        }
      } elseif ( 'prev' === $post_id ) {
        $prev = get_previous_post();

        if ( $prev && is_a( $prev, 'WP_Post' ) && $current_id !== $prev->ID ) {
          $post = $prev;
        }
      } elseif ( 'parent' === $post_id ) {
        $wp_post = get_post();
        if (!empty($wp_post->post_parent)) {
          $post = get_post($wp_post->post_parent);
        }
      } elseif ( (int) $post_id > 0 ) {
        $post = get_post( (int) $post_id );
      }

      $this->cache[$key] = is_a( $post, 'WP_Post' ) ? $post : null;

    }

    return $this->cache[$key];

  }

  public function get_contextual_term( $args = [] ) {


    // Resolve term by ID
    if ( isset( $args['term'] ) ) {
      $key = 'term:' . $args['term'];
      if ( ! isset( $this->cache[$key] ) ) {
        $this->cache[$key] = get_term( (int) $args['term'] );
      }
      return $this->cache[$key];
    }

    // Get the current looper data and queried object
    $data = CS()->component('Looper_Manager')->get_current_data();
    $queried = get_queried_object();

    // Priority 1: Return the current term from looper data
    if ( is_a( $data, 'WP_Term') ) {
      return $data;
    }

    // Priority 2: Return the current queried object if is a term
    if (is_a( $queried, 'WP_Term') ) {
      return $queried;
    }

    // Priority 3: If we are looping over a post, use its first term
    if (is_a( $data, 'WP_Post') ) {
      return $this->get_cached_first_term_for_post( $data );
    }

    // Priority 4: If the current query is a post, use its first term
    if (is_a( $queried, 'WP_Post') ) {
      return $this->get_cached_first_term_for_post( $queried );
    }

    return null;

  }

  public function get_cached_first_term_for_post( $post ) {

    $key = 'term-post:' . $post->ID;

    if ( ! isset( $this->cache[$key] ) ) {
      $first_post_term = $this->get_first_term_for_post( $post );
      $this->cache[$key] = $first_post_term  ? $first_post_term : null;
    }

    return $this->cache[$key];

  }

  public function get_first_term_for_post( $post ) {

    if ($post->post_type === 'post') {

      $post_cat = get_the_category( $post->id );
      if ( isset( $post_cat[0] ) && is_a( $post_cat[0], 'WP_Term' ) ) {
        return $post_cat[0];
      }

      $post_tag = get_the_tags( $post->id );
      if ( isset( $post_tag[0] ) && is_a( $post_tag[0], 'WP_Term' ) ) {
        return $post_tag[0];
      }

    }

    if ($post->post_type === 'product') {

      $product_cat = get_the_terms( $post->ID, 'product_cat' );
      if ( isset( $product_cat[0] ) && is_a( $product_cat[0], 'WP_Term' ) ) {
        return $product_cat[0];
      }

      $product_tag = get_the_terms( $post->ID, 'product_tag' );
      if ( isset( $product_tag[0] ) && is_a( $product_tag[0], 'WP_Term' ) ) {
        return $product_tag[0];
      }

    }

    return null;
  }

  public function get_contextual_product( $args = [] ) {
    if ( isset( $args['product'] ) ) {
      $product_id = $args['product'];
    } else {
      global $product;
      $product_id = empty($product) ? get_the_ID() : $product->get_id();
    }

    return $this->get_cached_product( $product_id );
  }

  public function get_cached_product( $product_id ) {
    $key = "product:$product_id";

    if ( ! isset( $this->cache[$key] ) ) {
      $this->cache[$key] = wc_get_product( $product_id );
    }

    return $this->cache[$key];
  }

  public function get_contextual_author( $args ) {
    // If no global post, that means
    // author page has been reset
    global $post;

    // No global post try from author page
    if (!$post && is_author())  {
      $author_id = get_query_var( 'author' );

      if(!empty($author_id)){
        return $this->get_cached_user( $author_id );
      }
    }

    // From contextual post
    $contextualPost = $this->get_contextual_post( $args );
    if (empty($contextualPost)) {
      return null;
    }

    // From contextual
    return $this->get_cached_user( get_post_field( 'post_author', $contextualPost ) );
  }

  public function get_contextual_user( $args = [] ) {

    // Return a specific user
    if ( isset( $args['user'] ) ) {
      $user_id = $args['user'];
      if ('author' === $user_id ) {
        global $post;
        $user_id = get_post_field( 'post_author', apply_filters( 'cs_dynamic_content_post_id', $post->ID ) );
      }
      return $this->get_cached_user( $user_id );
    }

    // Return user if it is the current context in the Looper Provider
    $data = CS()->component('Looper_Manager')->get_current_data();

    if ( is_a( $data, 'WP_User') ) {
      return $data;
    }

    // Return the logged in user
    return $this->get_cached_user( get_current_user_id() );
  }

  public function get_cached_user( $user_id ) {
    if ( !$user_id ) {
      return null;
    }

    $key = "user:$user_id";
    if ( ! isset( $this->cache[$key] ) ) {
      $user = get_user_by( 'ID', (int) $user_id );
      if ( ! $user ) {
        return null;
      }
      $this->cache[$key] = $user;
    }

    return $this->cache[$key];
  }

  /**
   * Options to be used in Dynamic Content popup
   */
  public function registerDynamicOptions($type, $controls) {
    $this->dynamicOptions[$type] = $controls;

    // Auto setup filters if passed
    if (!empty($controls['filter']) && is_callable($controls['filter'])) {
      add_filter("cs_dynamic_options_" . $type, $controls['filter'], 10, 2);
      add_filter("cornerstone_app_choices_" . $type, $controls['filter'], 10, 2);
    }
  }

  /**
   * Get dynamic option controls
   * used in late data
   */
  public function getDynamicOptions() {
    return $this->dynamicOptions;
  }
}
