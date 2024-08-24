<?php

namespace Themeco\Cornerstone\Parsy;

interface LanguageParser {
  public function compile( $input );
  public function hash( $input );
}