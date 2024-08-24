<?php

namespace Themeco\Cornerstone\Documents;

use Themeco\Cornerstone\Elements\ShortcodeContentBuilder;
use Themeco\Cornerstone\Util\Factory;

class Content extends Document {

  protected $permissionContext = '__';
  protected $type = 'content';
  protected $is_cornerstone_content = false;
  protected $is_legacy = false;

  protected $storedSettings = [
    'customCSS',
    'customJS',
    'layoutSingle',
    'layoutHeader',
    'layoutFooter'
  ];

  public function defaultSettings() {
    return [
      'customCSS'              => '',
      'customJS'               => '',
      'layoutSingle'           => 'default',
      'layoutHeader'           => 'default',
      'layoutFooter'           => 'default',
      'general_post_title'     => $this->title,
      'general_post_type'      => 'page',
      'general_post_name'      => '',
      'general_post_status'    => 'draft',
      'general_allow_comments' => false,
      'general_post_parent'    => '0',
      'general_page_template'  => apply_filters( 'cs_default_page_template', 'default' ),
      'general_manual_excerpt' => '',
      'general_featured_image' => '',
    ];
  }

  public function getDocType() {
    return 'content:' . $this->post->post_type;
  }

  public function builderInfo() {
    return [
      'editUrl' => get_edit_post_link( $this->id, '' ),
      'settingControls' => $this->getSettingControls(),
      'documentUrl' => $this->getPreviewUrl()
    ];
  }

  public function loadSettingsContent() {

    $settings = cs_get_serialized_post_meta( $this->post->ID, '_cornerstone_settings', true );

    if ( is_null( $settings ) ) {
      $settings = [];
    } else {
      $this->is_cornerstone_content = true; // We know this is Cornerstone content because settings exist
    }

    if (isset( $settings['custom_css'] ) && ! isset( $settings['customCSS'] ) ) {
      $settings['customCSS'] = $settings['custom_css'];
      unset($settings['custom_css']);
    }

    if (isset( $settings['custom_js'] ) && ! isset( $settings['customJS'] ) ) {
      $settings['customJS'] = $settings['custom_js'];
      unset($settings['custom_js']);
    }

    // Setup and grab thumbnail
    $settings = $this->grabThumbnail($this->post, $settings);

    return $settings;

  }

  public function readPostData() {

    $this->permissionContext = 'content.' . $this->post->post_type;

    $settings = $this->loadSettingsContent();

    $migrate = [];

    if ( isset( $settings['responsive_text'] ) ) {
      foreach ($settings['responsive_text'] as $el) {
        $migrate[] = [
          '_type'  => 'responsive-text',
          '_label' =>  $el['title'],
          'selector' =>  $el['selector'],
          'compression' =>  $el['compression'],
          'min_size' =>  $el['min_size'],
          'max_size' =>  $el['max_size'],
        ];
      }

      $settings['responsive_text'] = [];

    }

    $elements = $this->loadElementContent( $migrate );

    $this->detectLegacyContent( $elements );



    if ( post_type_supports( $this->post->post_type, 'title' ) )  {
      $settings['general_post_title'] = $this->post->post_title;
      $this->title = $this->post->post_title;
    }

    $settings['general_post_name'] = $this->post->post_name;
    $settings['general_post_status'] = $this->post->post_status;
    $settings['general_post_type'] = $this->post->post_type;

    if (post_type_supports($this->post->post_type, 'comments')) {
      $settings['general_allow_comments'] = $this->post->comment_status === 'open';
    }

    if (post_type_supports($this->post->post_type, 'excerpt')) {
      $settings['general_manual_excerpt'] = $this->post->post_excerpt;
    }

    $post_type_obj = get_post_type_object( $this->post->post_type );
    if ($post_type_obj->hierarchical) {
      $settings['general_post_parent'] = $this->post->post_parent;
    }

    // Load page template from wordpress
    if (
      (
        post_type_supports( $this->post->post_type, 'page-attributes' )
        || $this->post->post_type === "post"
      )
      && apply_filters('cs_use_wordpress_page_template', true)
    ) {
      $selected = get_post_meta($this->post->ID, '_wp_page_template', true);
      $settings['general_page_template'] = $selected ? $selected : 'default';
    }

    return [$elements, $settings];

  }

  public function loadElementContent( $migrate = []) {
    $elements = cs_get_serialized_post_meta( $this->post->ID, '_cornerstone_data', true, 'cs_content_load_serialized_content' );

    if ( ! is_array( $elements ) ) {
      $elements = [];
    } else {
      $this->is_cornerstone_content = true;
    }

    if ( isset( $elements['data'] ) ) {
      $elements = $elements['data'];
    }

    if ( $elements ) {
      foreach ($migrate as $element) {
        $elements[] = $element;
      }


      $elements = cornerstone('Elements')->migrations()->migrate( $elements );
    }

    return $elements;

  }


  public function detectLegacyContent( $elements ) {
    if ( isset( $elements ) && is_array( $elements ) && count( $elements ) > 0 ) {
      $top_level = array_filter( $elements, [ $this, 'detect_legacy_content_from_element'] );
      $this->is_legacy = count($top_level) === count( $elements );
    }
  }

  public function detect_legacy_content_from_element( $element ) {

    if (false === strpos( $element['_type'], 'classic:' ) ) {
      return false;
    }

    if ( isset($element['_modules'] ) ) {
      $classic_children = array_filter( $element['_modules'], [ $this, 'detect_legacy_content_from_element'] );
      return count( $classic_children ) === count( $element['_modules'] );
    }

    return true;

  }

  public function isLegacy() {
    return $this->is_legacy;
  }

  public function is_cornerstone_content() {
    return $this->is_cornerstone_content;
  }

  public function getPreviewUrl() {

    if ( $this->wpml->is_active() ) {
      $this->wpml->before_get_permalink();
      $permalink = apply_filters( 'wpml_permalink', get_permalink( $this->post ), apply_filters('cs_locate_wpml_language', null, $this->post ) );
      $this->wpml->after_get_permalink();
      return $permalink;
    }

    return get_permalink( $this->post );
  }

  public function update( $update ) {

    if ( isset( $update['settings'] ) ) {
      if ( ! current_user_can( 'unfiltered_html' ) ) {
        unset( $update['settings']['custom_js'] );
        unset( $update['settings']['custom_css'] );
      }
      $this->data['settings'] = array_merge( $this->data['settings'], cs_sanitize( $update['settings'] ) );
    }

    if ( isset( $update['elements'] ) ) {
      $this->purgeElementData();
      $this->data['elements'] = [ 'data' => cs_sanitize( $update['elements'] ) ];
    }

    if ( isset( $update['clone'] ) ) {
      list($elements, $settings) = self::locate( (int) $update['clone'] )->cloneDoc();
      $clone = self::locate( (int) $update['clone'] )->cloneDoc();
      $this->data['settings'] = $settings;
      $this->data['elements'] = [
        'data' => $elements
      ];

      unset($this->data['settings']['assignments']);
      unset($this->data['settings']['assignment_priority']);
    }

    if ( isset( $update['title'] ) ) {
      $title = cs_sanitize( $update['title'] );
      if ($title) {
        $this->title = $title;
        $this->data['settings']['general_post_title'] = $title;
      }
    }

    // For permission usage
    if (isset($update['type'])) {
      $this->permissionContext = $this->type . '.' . static::resolvePostTypeForDocType($update['type']);
    }

    return $this;
  }

  public function applyPostTypeSettings( $update ) {

    $update['post_type']   =  $this->data['settings']['general_post_type'];
    $update['post_status'] = $this->data['settings']['general_post_status'];

    $post_type = $this->data['settings']['general_post_type'];
    $post_type_object = get_post_type_object( $this->data['settings']['general_post_type'] );

    if (post_type_supports( $post_type, 'comments' ) ) {
      $update['comment_status'] = ( true === $this->data['settings']['general_allow_comments'] ) ? 'open' : 'closed';
    }

    if ( post_type_supports( $post_type, 'excerpt' ) && isset( $this->data['settings']['general_manual_excerpt'] ) ) {
      $update['post_excerpt'] = $this->data['settings']['general_manual_excerpt'];
    }

    if ($post_type_object->hierarchical) {
      $update['post_parent'] = (int) $this->data['settings']['general_post_parent'];
    }

    if ( post_type_supports( $post_type, 'page-attributes' ) && isset($this->data['settings']['general_page_template']) ) {
      $update['page_template'] = $this->data['settings']['general_page_template'];
    }

    return $update;

  }

  public function save() {

    // Settings Update
    // ---------------

    $update = array(
      'post_title'  => $this->data['settings']['general_post_title'],
      'post_name'   => $this->data['settings']['general_post_name'],
    );

    $update = $this->applyPostTypeSettings( $update );
    $data = $this->data;

    $is_update = is_int( $this->id );

    if ( $is_update ) {
      $update['ID'] = $this->id;
    }

    $id = $is_update ? wp_update_post( $update ) : wp_insert_post( $update );

    if ( is_wp_error( $id ) ) {
      return $id;
    }

    if ( 0 === $id ) {
      return new \WP_Error('cs-content', "Unable to save content: $id");
    }

    $this->id = $id;

    $previous_settings = $is_update ? cs_get_serialized_post_meta( $this->id, '_cornerstone_settings', true ) : [];
    if (is_null($previous_settings)) {
      $previous_settings = [];
    }

    $settings_update = [];
    foreach ( $this->storedSettings as $storedKey ) {
      if (isset($data['settings'][$storedKey])) {
        $settings_update[$storedKey] = $data['settings'][$storedKey];
      }
    }

    $settings_update['responsive_text'] = [];


    $settings_update = apply_filters( 'cs_content_settings_update', array_merge($previous_settings, $settings_update), $this->id );

    cs_update_serialized_post_meta( $this->id, '_cornerstone_settings', $settings_update, '', false, 'cs_content_update_serialized_content' );


    // Elements Update
    // ---------------

    //Valid if empty, however this is probably a bug in the code
    if(empty($this->data['elements']['data'])) {
      $this->data['elements']['data'] = [];
    }

    $update_elements = $this->updateElements( $this->data['elements']['data'], $settings_update );

    // Save thumbnail
    $this->saveThumbnail($this->post, $this->data['settings']);

    // Document save action
    do_action("cs_save_document", $this );

    if ( is_wp_error( $update_elements ) ) {
      throw new \Exception( 'Error saving content elements: ' . $update_elements->get_error_message() );
    }

    return self::locate($id)->serialize();

  }

  public function updateElements( $elements, $settings ) {


    if ( ! is_array( $elements ) ) {
      return;
    }

    $outputBuilder = Factory::create(ShortcodeContentBuilder::class);
    $output = $outputBuilder->build_output( $elements, $settings, $this->is_legacy );


		if ( is_wp_error( $output ) ) {
			return $output;
		}

    if ( $this->is_legacy ) {
      $output['buffer'] .= $this->getResponsiveText();
    }

    do_action( 'cornerstone_before_save_content', $this->id );

		cs_update_serialized_post_meta( $this->id, '_cornerstone_data', $output['data'], '', false, 'cs_content_update_serialized_content' );
		delete_post_meta( $this->id, '_cornerstone_override' );
    delete_post_meta( $this->id, '_cs_generated_tss');

    $post_content = apply_filters( 'cornerstone_save_post_content', $output['content'], $settings );

		$id = wp_update_post( array(
			'ID'           => $this->id,
      'post_content' => wp_slash( "[cs_content _p='{$this->id}']" . $post_content . '[/cs_content]'),
    ) );

    if ( is_wp_error( $id ) ) {
      return $id;
    }

    if ( 0 === $id ) {
      return new \WP_Error('cs-content', "Unable to save content: $id");
    }

    do_action( 'cornerstone_after_save_content', $this->id );

    return true;
  }

  public function getResponsiveText() {
    $content = '';

    if (isset($this->data['settings']['responsive_text']) && count($this->data['settings']['responsive_text']) > 0) {
      foreach ($this->data['settings']['responsive_text'] as $element ) {
        $content .= cs_build_shortcode('cs_responsive_text', $element );
      }
    }

    return $content;
  }


  public function isAllowed( $type = '') {
    // Can create
    if ( $type === 'create' ) {
      $post_type_obj = get_post_type_object( $this->data['settings']['general_post_type'] );
      $caps = (array) $post_type_obj->cap;
      return current_user_can( $caps['create_posts'] ) && parent::isAllowed($type);
    }

    // Can delete
    if ( $type === 'delete' ) {
      return (
        current_user_can( $this->permissions->getPostTypeCapability( $this->post->post_type, 'delete_posts' ), $this->post->ID)
        && parent::isAllowed($type)
      );
    }

    if (in_array( $this->post->ID, cs_get_disallowed_ids(), true )) {
      return false;
    }

    $post_type_obj = get_post_type_object( $this->post->post_type );
    $caps = (array) $post_type_obj->cap;

    if (! current_user_can( $caps['edit_post'], $this->post->ID ) || ! $this->permissions->userCanAccessPostType($this->post->post_type) ) {
      return false;
    }

    return true;

  }

  public function getStylePriority() {
    return [40, 100];
  }


  public function getSettingControls() {
    if (!isset($this->post)) {
      return array();
    }
    return $this->builderSettingsControls($this->post);
  }

  public function builderSettingsControls($post) {

    $post_type_obj = get_post_type_object( $post->post_type );
    $controls = [];

    //
    // General
    //

    $general_controls = array();

    if ( post_type_supports( $post->post_type, 'title' ) ) {
      $general_controls[] = array(
        'key' => 'general_post_title',
        'type' => 'text',
        'label' => __( 'Title', 'cornerstone' ),
      );
    }

    $general_controls[] = array(
      'key' => 'general_post_name',
      'type' => 'text',
      'label' => __( 'Slug', 'cornerstone' ),
    );

    $general_controls[] = array(
      'key' => 'general_post_status',
      'type' => 'select',
      'label' => __( 'Status', 'cornerstone' ),
      'options' => array(
        'choices' => cs_get_filtered_post_status_choices( $post )
      ),
      'condition' => array(
        'user_can:{context}.publish' => true
      )
    );

    if (post_type_supports($post->post_type, 'comments')) {
      $general_controls[] = array(
        'key' => 'general_allow_comments',
        'type' => 'toggle',
        'label' => __( 'Allow Comments', 'cornerstone' ),
      );
    }

    if (post_type_supports($post->post_type, 'excerpt')) {
      $general_controls[] = array(
        'key' => 'general_manual_excerpt',
        'type' => 'textarea',
        'label' => __( 'Manual Excerpt', 'cornerstone' ),
        'options' => array(
          'placeholder' => __( '(Optional) An excerpt will be derived from any paragraphs in your content. You can override this by crafting your own excerpt here, or in the WordPress post editor.', 'cornerstone' )
        )
      );
    }

    if ($post_type_obj->hierarchical) {
      $general_controls[] = array(
        'key' => 'general_post_parent',
        'type' => 'select',
        'label' => sprintf( __( 'Parent %s', 'cornerstone' ), $post_type_obj->labels->singular_name),
        'options' => array(
          'choices' => cs_get_filtered_post_parent_choices( $post )
        )
      );
    }

    if (post_type_supports($post->post_type , 'page-attributes')) {
      $page_templates = cs_get_page_template_options($post->post_type, $post );
      if (count($page_templates) > 1) {
        $general_controls[] = [
          'key' => 'general_page_template',
          'type' => 'select',
          'label' => __( 'Page Template', 'cornerstone' ),
          'options' => [ 'choices' => $page_templates ]
        ];
      }
    }

    // Thumbnail / featured image
    if (post_type_supports($post->post_type, 'thumbnail')) {
      $general_controls[] = [
        'key' => 'general_featured_image',
        'type' => 'image',
        'label' => __( 'Featured Image', 'cornerstone' ),
      ];
    }


    $controls[] = array(
      'type'  => 'group',
      'label' => __('General', 'cornerstone'),
      'options' => [
        'actions' => [
          [
            'key' => 'edit_in_wordpress',
            'icon' => 'info',
            'type' => 'wordpress-edit',
            'label' => __( 'Edit in Wordpress', 'cornerstone' ),
          ],
        ],
      ],
      'controls' => $general_controls
    );

    if ( $this->env->isSiteBuilder() ) {
      $controls[] = array(
        'type'  => 'group',
        'label' => __('Layout Assignments', 'cornerstone'),
        'controls' => [
          [
            'key' => 'layoutSingle',
            'type' => 'select',
            'label' => __( 'Single', 'cornerstone' ),
            'options' => [
              'choices' => 'dynamic:documents-layout-single'
            ]
          ], [
            'key' => 'layoutHeader',
            'type' => 'select',
            'label' => __( 'Header', 'cornerstone' ),
            'options' => [
              'choices' => 'dynamic:documents-layout-header'
            ]
          ], [
            'key' => 'layoutFooter',
            'type' => 'select',
            'label' => __( 'Footer', 'cornerstone' ),
            'options' => [
              'choices' => 'dynamic:documents-layout-footer'
            ]
          ]
        ]
      );
    }

    $controls = apply_filters('cs_builder_settings_controls', $controls, $post, $this );

    return $controls;
  }




  public function transformElements() {
    $elements = isset($this->data['elements']) ? $this->data['elements'] : [];

    return [
      '_type' => 'root',
      '_modules' => [
        [
          '_type'    => 'region',
          '_region'  => 'content',
          '_modules' => is_array($elements) ? $elements : []
        ]
      ]
    ];
  }

}
