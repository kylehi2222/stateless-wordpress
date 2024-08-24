<?php

namespace Themeco\Cornerstone\Services;

class FrontEnd implements Service {

  protected $excerpt_post_id = null;
  protected $the_content_cache = array();

  protected $resolver;
  protected $assignments;

  public function __construct(Resolver $resolver, Assignments $assignments) {
    $this->resolver = $resolver;
    $this->assignments = $assignments;
  }

  public function setup() {
    add_action( 'init', [ $this, 'init' ] );
  }

  public function init() {

    // Set active content to assignments
    add_action('template_redirect', function() {
      $content = $this->resolver->getDocument( $this->get_post_id() );
      if ( $content ) {
        $this->assignments->setActiveContent( $content );
      }
    });




    add_filter('template_include', array( $this, 'setup_after_template_include' ), 99998 );

		// Excerpt related functions
		add_filter( 'strip_shortcodes_tagnames', array($this, 'preserve_excerpt'), 999999 );
		add_filter( 'the_content', array( $this, 'maybe_noemptyp' ), 10 );
		add_shortcode( 'cs_content_seo', array( $this, 'cs_content_seo' ) );

    add_action( 'cs_late_template_redirect', array( $this, 'post_loaded' ), 9998, 0 );
    add_shortcode( 'cs_content', array( $this, 'render_content') );

    $this->track_excerpt();
  }

  public function track_excerpt() {
    add_filter( 'get_the_excerpt', [ $this, 'begin_excerpt'], 0, 2 );
    add_filter( 'get_the_excerpt', [ $this, 'end_excerpt'], 1000 );
  }

  public function untrack_excerpt() {
    remove_filter( 'get_the_excerpt', [ $this, 'begin_excerpt'], 0 );
    remove_filter( 'get_the_excerpt', [ $this, 'end_excerpt'], 1000 );
  }

  public function allow_cs_content( $tags ) {
    $index = array_search( 'cs_content', $tags);
    if ($index) {
      unset( $tags[ $index ] );
    }
    return $tags;
  }

  public function begin_excerpt( $text, $post = 0 ) {
    $this->excerpt_post_id = $post === 0 ? null : $post->ID;
    add_filter( 'strip_shortcodes_tagnames', [ $this, 'allow_cs_content' ] );
    return $text;
  }

  public function end_excerpt( $text ) {
    $this->excerpt_post_id = null;
    remove_filter( 'strip_shortcodes_tagnames', [ $this, 'allow_cs_content' ] );
    return $text;
  }

  public function render_content( $atts, $content ) {

    $_p = null;
    $wrap = null;

    extract( shortcode_atts( array(
      '_p' => $this->get_post_id(),
      'wrap' => true
    ), $atts, 'cs_content' ) );

    $content = $this->render_the_content_cached($_p, $content);

    if ( $wrap && $wrap !== 'false') {
      return cs_tag( 'div', apply_filters( 'cs_content_atts', array(
        'id'    => 'cs-content',
        'class' => 'cs-content',
      ), get_the_ID(), get_post_type() ), $content );
    }

    return $content;

  }

  public function render_the_content_cached($_p, $content) {

    $doc = $this->resolver->getDocument( $_p );

    if ( is_null( $doc ) ) {
      return '';
    }

    // If this item is purely classic elements, rely on the shortcode output
    if ($doc->isLegacy()) {

      // Manually apply the render stack
      // 1. do_shortcode('[cs_content]') happens without an ID present causing it to be called with the previous id
      // 2. A post somehow attempts to call itself

      return $this->resolver->safeDocRender($doc, function() use ($content) {
        return cs_dynamic_content( do_shortcode( $content ) );
      });

    }

    // This cache prevents [cs_content] from being rendered multiple times on the front end. e.g an SEO plugin requesting the_content to generate meta tags
    if ( ! isset( $this->the_content_cache[$_p] )) {

      if ($doc->is_cornerstone_content()) {
        $this->the_content_cache[$_p] = $this->resolver->renderContentFromDocument($doc);
      } else {
        $this->the_content_cache[$_p] = '';
      }
    }

    return $this->the_content_cache[$_p];

  }

  public function post_loaded() {
    if ( ! is_singular()) {
      return;
    }

    if ( did_action( 'cs_before_preview_frame_content') || did_action( 'cs_before_preview_frame_component') ) {
      return;
    }

    $content = $this->assignments->getActiveContent();

    if ( is_null( $content ) || ! $content->is_cornerstone_content() ) {
      return;
    }

    $this->resolver->loadDocument( $content );

  }

  public function get_post_id() {
    $id = in_the_loop() ? get_the_ID() : get_queried_object_id();
    if ( $this->excerpt_post_id ) {
      $id = $this->excerpt_post_id;
    }
    return (int) apply_filters( 'cs_element_post_id', $id );
  }

	/**
	 * A late template_redirect hook allows plugins like Custom 404 and Under Construction
	 * to modify the query before we assume we can query info like the current ID
	 */
	public function setup_after_template_include( $template ) {
		do_action('cs_late_template_redirect');
		return $template;
	}


	/**
	 * Preserve content of [cs_content_seo][/cs_content_seo] making it visible for excerpt generation.
	 */
	public function preserve_excerpt ( $tags ) {
		return array_diff ($tags, array('cs_content_seo'));
	}

	/**
	 * Cornerstone adds a wrapping [cs_content] shortcode. Run the content through
	 * cs_noemptyp if we know it was originally generated by Cornerstone.
	 * This cleans up any empty <p> tags.
	 * @param  string $content Early the_content. Before do_shortcode
	 * @return string          the_content with empty <p> tags removed and wrapping div
	 */
	public function maybe_noemptyp( $content ) {

		if ( false !== strpos( $content, '[cs_content]' ) && false !== strpos( $content, '[/cs_content]' ) ) {
			$content = cs_noemptyp( $content );
		}

		return $content;

	}

	public function cs_content_seo ($atts, $content) {

    $output = false;

		extract( shortcode_atts( array(
			'output'      => false
		), $atts, 'cs_content_seo' ) );

		if ( $output || doing_filter ('get_the_excerpt') ) return cs_dynamic_content($content);

		return '';

	}

}
