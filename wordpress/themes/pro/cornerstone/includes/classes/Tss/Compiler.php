<?php

namespace Themeco\Cornerstone\Tss;
use Themeco\Cornerstone\Parsy\Util\Token;
use Themeco\Cornerstone\Parsy\Serializer;

class Compiler {
  public $parser;
  public $serializer;

  public function __construct(StyleParser $parser, Serializer $serializer) {
    $this->parser = $parser;
    $this->serializer = $serializer;
  }

  public function setup() {
    $this->parser->setup();
  }

  public function run($input) {
    return $this->serializer->serialize(new Token('document', $this->parse($input)));
  }

  /**
   * Get statements for usage in a module
   */
  public function getParserStatement($tss = "") {
    return $this->parser->run($tss)->content();
  }

  /**
   * Parse TSS to statements
   */
  public function parse($filename) {
    if (!file_exists($filename)) {
      throw new \Exception("Input file does not exist: $filename");
    }

    $tss = file_get_contents($filename);

    if (!$tss) return [];

    $base = dirname($filename) . '/';

    try {
      $statements = $this->parser->run($tss)->content();
    } catch  (\Throwable $e) {
      $message = $e->getMessage();
      throw new \Exception( "$message $filename");
    }

    return $this->getCombinedStatements($base, $statements);
  }

  public function getCombinedStatements($base, $statements) {
    $combined = [];
    foreach ($statements as $statement) {
      if ($statement->is('import')) {
        $combined = array_merge( $combined, $this->parse( $base . $statement->content()->content() . '.tss'));
      } else {
        $combined[] = $statement;
      }
    }

    return $combined;
  }
}
