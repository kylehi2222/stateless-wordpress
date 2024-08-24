<?php

class MeowPro_MWAI_FunctionAware {
  private $core = null;

  function __construct( $core ) {
    $this->core = $core;
    // Register the functions from Snippet Vault.
    add_filter( 'mwai_functions_list', array( $this, 'functions_list' ), 10, 1 );
    // Handle the feedbacks for the functions created via Snippet Vault.
    add_filter( 'mwai_ai_feedback', array( $this, 'ai_feedbacks' ), 10, 2 );
    // Add the functions to the chatbot query.
    add_filter( 'mwai_chatbot_query', array( $this, 'chatbot_query' ), 10, 2 );
  }

  /**
   * Create a Meow_MWAI_Query_Function object based on type and id
   *
   * @param string $type
   * @param string $id
   * @return Meow_MWAI_Query_Function|null
   */
  public static function get_function( $funcType, $funcId ) {
    global $mwai_core;
    $functions = apply_filters( 'mwai_functions_list', [] );
    foreach ( $functions as $function ) {
      if ( $function->type === $funcType && $function->id === $funcId ) {
        return $function;
      }
    }
    $mwai_core->log( "⚠️ (Functions) The function '{$funcId}' was not found." );
    return null;
  }

  // Add the functions from Snippet Vault
  function functions_list( $functions ) {
    global $mwcode;
    if ( isset( $mwcode ) ) {
      $svFuncs = $mwcode->get_functions();
      foreach ( $svFuncs as $function ) {
        $function['type'] = 'snippet-vault';
        $func = Meow_MWAI_Query_Function::fromJson( $function );
        $functions[] = $func;
      }
    }
    return $functions;
  }

  // Handle the feedbacks for the functions created via Snippet Vault.
  function ai_feedbacks( $value, $functionCall ) {
    $function = $functionCall['function'];
    if ( empty( $function ) || empty( $function->id ) ) {
      return $value;
    }
    if ( $function->type !== 'snippet-vault' ) {
      return $value;
    }

    // Not sure why Anthropic is sending an object with a type of 'object' when there is nothing
    // in the object. This is a workaround for that.
    $arguments = $functionCall['arguments'] ?? [];
    if ( is_array( $arguments ) && count( $arguments ) === 1 && 
      isset( $arguments['type'] ) && $arguments['type'] === 'object' ) {
      $arguments = [];
    }

    // Execute the function with Snippet Vault.
    global $mwcode;
    if ( empty( $mwcode ) ) {
      $this->core->log( "⚠️ (Functions) Snippet Vault is not available." );
      return $value;
    }
    $value = $mwcode->execute_function( $function->id, $functionCall['arguments'] );
    return $value;
  }

  function chatbot_query( $query, $params ) {
    $functions = $params['functions'] ?? [];
    foreach ( $functions as $function ) {
      $query_function = self::get_function( $function['type'] ?? null, $function['id'] ?? null );
      if ( $query_function ) {
        $query->add_function( $query_function );
      }
    }
    return $query;
  }
}
