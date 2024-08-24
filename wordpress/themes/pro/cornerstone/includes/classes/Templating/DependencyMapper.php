<?php

namespace Themeco\Cornerstone\Templating;

class DependencyMapper {

  protected $exporter;
  protected $templateData;
  protected $template;
  protected $post;
  protected $isTemplate;
  protected $json;
  protected $dependencies = [];

  public function setup($exporter, $template, $post, $isTemplate ) {
    $this->exporter = $exporter;
    $this->template = $template;
    $this->post = $post;
    $this->isTemplate = $isTemplate;
    return $this;
  }

  public function getColorIds() {
    return array_map(function($item) {
      return $item['_id'];
    }, $this->exporter->getColors());
  }

  public function getFontIds() {
    return array_map(function($item) {
      return $item['_id'];
    }, $this->exporter->getFonts());
  }

  public function matchColors() {
    $matchInPlace = $this->getColorIds();

    if (count($matchInPlace) <= 0) {
      return [];
    }
    $matchInPlacePattern = "(" . implode("|",$matchInPlace) . ")";
    preg_match_all($matchInPlacePattern, $this->json, $matches);

    foreach ($matches[0] as $colorId) {
      $this->addDependency('global-color', $colorId);
    }

  }

  public function addDependency($group, $value, $wrap = false) {
    $key = is_string($value) ? $value : json_encode($value);
    $hash = $this->exporter->hash($group, $key);
    $this->dependencies[$hash] = [$group, $value];
    return $wrap ? "_cs-tmpl:{$hash}:cs-tmpl_" : $hash;
  }

  public function matchFonts() {
    $matchInPlace = $this->getFontIds();

    if (count($matchInPlace) <= 0) {
      return [];
    }
    $matchInPlacePattern = "(" . implode("|",$matchInPlace) . ")";
    preg_match_all($matchInPlacePattern, $this->json, $matches);

    foreach ($matches[0] as $fontItemId) {
      $this->addDependency('global-font', $fontItemId);
    }
  }

  public function matchImageUris() {
    $this->json = preg_replace_callback('#(?:https?:)?\/\/[a-zA-Z0-9\-\%\_\s\\\.\/]*\.(?:jpg|jpeg|jpe|gif|png|bmp|tif|tiff|ico|svg)#i',function($matches) {
      return $this->addDependency('image-uri', $matches[0], true);
    },$this->json);
  }


  public function addAttachmentId( $id, $wrap = false ) {
    $attachment_meta = wp_get_attachment_image_src( $id, 'full' );

    // not valid
    if (empty($attachment_meta)) {
      $attachment_meta = [''];
    }

    return $this->addDependency('image-attachment', [$id, $attachment_meta[0]], $wrap);
  }

  public function matchImageIds() {
    $ids = $this->exporter->getAttachmentIds();

    // "bgImg": \"image|12345:full"
    // "initial": \"12345:full"
    // "key": "471:full"
    $this->json = preg_replace_callback('#"\s*?:\s*?\\\?"(?:image\|)?([\d]+:[\w]+)#',function($matches) use ($ids) {
      $match = $matches[1];
      list($id, $size) = explode(':',$match);
      $id = (int) $id;
      if ( ! in_array( $id, $ids ) ) { // ignore ids that don't exist in the DB
        return $matches[0];
      }

      return str_replace($match, $this->addAttachmentId( $id, true ), $matches[0]);
    },$this->json);

  }

  public function matchComponentInvocationIds() {

    list($componentData) = $this->exporter->components->enumerate();
    $ids = array_keys($componentData);

    preg_match_all('#component_id\\\?"\s*?:\s*?\\\?"([-\w_]+)\\\?"#', $this->json, $matches);

    foreach ($matches[1] as $id) {
      if ( in_array( $id, $ids ) && isset( $componentData[$id] ) && isset($componentData[$id]['doc']) ) { // ignore ids that don't exist in the DB
        $this->addDependency('component', (int) $componentData[$id]['doc']);
      }
    }

  }

  public function matchComponentDocumentIds() {
    $ids = $this->exporter->getComponentDocumentIds();

    $this->json = preg_replace_callback('#global_block_id\\\?":\s*?\\\?"([\d]+)\\\?"#',function($matches) use ($ids) {
      if ( ! in_array( (int) $matches[1], $ids ) ) { // ignore ids that don't exist in the DB
        return $matches[0];
      }
      return str_replace($matches[1], $this->addDependency('component', (int) $matches[1], true), $matches[0]);
    },$this->json);

  }

  public function addTerms() {
    $taxonomies = get_object_taxonomies( $this->post->post_type );
    $added = [];
    if ( !empty( $taxonomies ) ) {
      $terms = wp_get_object_terms( $this->post->ID, $taxonomies );
      foreach ($terms as $term) {
        if ( !isset( $added[$term->taxonomy] ) ) $added[$term->taxonomy] = [];
        $added[$term->taxonomy][] = $this->addDependency( 'term', $term->term_id, true );
      }
    }
    return $added;
  }


  public function matchTerms() {
    $this->addTerms();
    $terms = $this->addTerms();
    if ( ! empty ( $terms ) ) {
      $this->templateData['terms'] = $terms;
    }
  }

  public function matchLayout( $type ) {
    if ( isset( $this->templateData['meta']['settings'][$type] ) && $this->templateData['meta']['settings'][$type] !== 'default' ) {
      $this->templateData['meta']['settings'][$type] = $this->addDependency('doc', (int) $this->templateData['meta']['settings'][$type], true);
    }
  }


  public function enumerate() {

    $this->template->loadMeta();
    $this->templateData = $this->template->serialize();

    if ($this->exporter->getOption('excludeThumbnails')) {
      unset($this->templateData['preview']);
    }

    if ( ! $this->isTemplate ) {

      if ( $this->post->post_parent ) {
        $this->templateData['parent'] = $this->addDependency('doc', $this->post->post_parent);
      }

      $thumbnail_id = get_post_thumbnail_id( $this->post );

      if ($thumbnail_id) {
        $this->templateData['thumbnail'] = $this->addAttachmentId($thumbnail_id, true);
      }

      if ($this->post->ID === (int) get_option('page_on_front') ) {
        $this->templateData['page_on_front'] = true;
      }

      if ($this->post->ID === (int) get_option('page_for_posts') ) {
        $this->templateData['page_for_posts'] = true;
      }

      $this->matchLayout('layoutSingle');
      $this->matchLayout('layoutHeader');
      $this->matchLayout('layoutFooter');
      $this->matchTerms();

    }

    // convert to JSON so we can do global string replacements
    $this->json = json_encode($this->templateData['meta'], JSON_UNESCAPED_SLASHES);
    unset($this->templateData['id']);

    $this->matchColors();
    $this->matchFonts();
    $this->matchImageUris();
    $this->matchImageIds();
    $this->matchComponentDocumentIds();
    $this->matchComponentInvocationIds();

    $this->templateData['sig'] = $this->exporter->hash('sig', $this->post->ID);
    $this->templateData['meta'] = json_decode($this->json, true);

    return [$this->templateData, $this->dependencies];

  }
}
