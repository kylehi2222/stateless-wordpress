<?php

/**
 * Element Definition: Text
 */

class CSE_Text extends BaseClassicElement {

	public function ui() {
		return array(
      'title'       => __( 'Text', 'cornerstone' ),
      'autofocus' => array(
    		'content' => '.x-text',
    	)
    );
	}

	public function register_shortcode() {
  	return false;
  }
	
  public function flags() {
		return array(
      'protected_keys' => array(
        'content'
      )
		);
	}

}
