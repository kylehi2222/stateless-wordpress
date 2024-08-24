<?php

namespace Themeco\Cornerstone\Services;

use Themeco\Cornerstone\Util\ConditionRules;
class RuleMatching implements Service {

  public function normalizeRuleSet( $rule_sets ) {
    $groups = [];
    $index = 0;

    foreach ($rule_sets as $set) {

      if ( isset( $set['group'] ) && $set['group'] ) {
        $index++;
      }
      if ( ! isset( $groups[$index] ) ) {
        $groups[$index] = [];
      }

      $groups[$index][] = [
        $set['condition'],
        $set['value'],
        isset( $set['toggle'] ) ? ! $set['toggle'] : null,
        isset( $set['operator'] ) ? $set['operator'] : null,
        isset( $set['operand'] ) ? $set['operand'] : null
      ];

    }

    return array_values($groups);
  }

  public function evaluate( $rule ) {

    list($type, $value, $invert, $operator, $operand) = $rule;

    $invert = empty( $invert) ? false : $invert;
    $operator = empty( $operator) ? null : $operator;

    $parts = explode( '|', $type );
    $handler = array_shift( $parts );
    $rule_name = str_replace(':', '_', str_replace('-', '_', $handler ) );

    if ( $operator ) $rule_name .= '_' . str_replace('-', '_', $operator );

    $base_args = is_null( $operand ) ? [ $value ] : [ $operand, $value ];
    $args = empty( $parts ) ? $base_args : array_merge( $parts, $base_args);

    $method = [ ConditionRules::class, $rule_name ];
    $is_callable = is_callable( $method );

    if ( ! has_filter( 'cs_condition_rule_' . $rule_name ) && ! $is_callable ) {
      trigger_error("No rule matching function for $rule_name ", E_USER_WARNING );
      return false;
    }

    $args = apply_filters('cs_condition_args', $args);

    $result = apply_filters('cs_condition_rule_' . $rule_name, $is_callable ? call_user_func_array( $method, $args ) : false, $args);

    return $invert && ! $operator ? ! $result : $result;
  }

  // A group matches if all of its rules are true
  public function matchRuleGroup( $rule_group ) {

    foreach ($rule_group as $rule) {
      if ( ! $this->evaluate($rule) ) {
        return false;
      }
    }

    return true;
  }

  // A set matches if any of its groups are true

  public function match( $rule_sets ) {

    $groups = $this->normalizeRuleSet( $rule_sets );

    foreach ($groups as $group) {
      if ( $this->matchRuleGroup( $group ) ) {
        return true;
      };
    }

    return false;

  }

  public function shouldHideElement( $data ) {

    // Classic Columns
    if ( isset( $data['_active'] ) && $data['_active'] === false) {
      return true;
    }

    if ( ! isset($data['show_condition']) || ! $data['show_condition'] ) {
      return false;
    }

    // Disable element conditions in the preview
    if ( apply_filters( 'cs_preview_disable_element_conditions', false ) ) {
      return false;
    }

    return ! $this->match( $data['show_condition'] );

  }
}
