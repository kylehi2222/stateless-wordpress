<?php

namespace Themeco\Cornerstone\Parsy;

class OrderOperations {

  const VALUE = 0;
  const OP = 1;

  protected $operatorPrecedence = [
    '*' => 1,
    '/' => 1,
    '%' => 1,
    '+' => 2,
    '-' => 2,
    '>' => 3,
    '>=' => 3,
    '<' => 3,
    '<=' => 3,
    '==' => 4,
    '===' => 4,
    '!=' => 4,
    '!==' => 4,
    'and' => 5,
    '&&' => 5,
    'xor' => 6,
    'or' => 6,
    '||' => 6,
  ];

  public function setOperatorPrecedence($ops) {
    $this->operatorPrecedence = $ops;
  }

  public function run( $input, $mapper ) {

    $stack = [];
    $postfix = $this->shuntingYard( $input );

    while( count( $postfix ) ) {
      list( $type, $next ) = array_shift($postfix);
      if ($type === self::VALUE) {
        $stack[] = $next;
        continue;
      }
      if ($type === self::OP) {
        $b = array_pop($stack);
        $a = array_pop($stack);
        $stack[] = $mapper([$a, $next, $b]);
      }
    }

    return end($stack);
  }

  // Take an input of operators and return a postfix notation version with sorted operators
  public function shuntingYard( $input ) {

    $output = [[self::VALUE,array_shift($input)]];
    $ops = [];

    try {


    while ( count( $input ) > 0 ) {
      $op = array_shift($input);
      $precedence = $this->operatorPrecedence[$op];

      while (count($ops) > 0 && $this->operatorPrecedence[end($ops)] < $this->operatorPrecedence[$op]) {
        $output[] = [self::OP,array_pop($ops)];
      }

      $ops[] = $op;
      $output[] = [self::VALUE,array_shift($input)];

    }

    while (count($ops) > 0) {
      $output[] = [self::OP,array_pop($ops)];
    }

    return $output;
  } catch (\Exception $e) {
    var_dump($e);
  }
  return [];
  }

}