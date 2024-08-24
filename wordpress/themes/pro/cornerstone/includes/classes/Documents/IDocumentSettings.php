<?php

namespace Themeco\Cornerstone\Documents;

interface IDocumentSettings {
  public function getStylePriority();
  public function getCustomCss();
  public function getCustomJs();
  public function id();
}