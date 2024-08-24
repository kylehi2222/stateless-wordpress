<?php

namespace Themeco\Cornerstone\Vm\Script;

use Themeco\Cornerstone\Vm\Constants;
use Themeco\Cornerstone\Parsy\Language;
use Themeco\Cornerstone\Parsy\OrderOperations;
use Themeco\Cornerstone\Parsy\P;

class NanoScript extends Language {

  protected static $binaryOps = [
    '*' => 'op-multiply',
    '/' => 'op-divide',
    '%' => 'op-modulus',
    '+' => 'op-add',
    '-' => 'op-subtract',
    '>' => 'op-lt',
    '>=' => 'op-lte',
    '<' => 'op-gt',
    '<=' => 'op-gte',
    '==' => 'op-eq',
    '===' => 'op-eq-strict',
    '!=' => 'op-not-eq',
    '!==' => 'op-not-eq-strict',
    'and' => 'op-and',
    'xor' => 'op-xor',
    'or' => 'or'
  ];

  private $orderOperations;

  public function __construct(OrderOperations $orderOperations) {
    $this->orderOperations = $orderOperations;
  }

  public function grammar() {
    return [
      'script' => function($l) {
        return P::whitespace()
          ->then(P::many1($l->statement->skip(P::laxTerminate())))
          ->skip(P::whitespace())
          ->skip(P::end())
          ->token('script');
      },

      'scalar' => function($l) {
        return P::any(P::scalarString(), P::scalarNumber(), P::scalarNull(), P::scalarTrue(), P::scalarFalse())->token('scalar');
      },

      'nsx' => function($l) {
        return P::symbol('<')->then(P::wsWord())->skip(P::symbol('/')->then(P::str('>')));
      },

      'unaryOperator' => P::regex('/\A\s*(?:not\s|[-+!])/')->trim(),
      'binaryOperator' => P::regex('/\A\s*(\*|\/|%|\+|-|<=|>=|<|>|==|!=|and\s|or\s)/')->trim(),
      'operandWithUnaryOperation' => function( $l ) {
        return $l->unaryOperator->otherwise(null)->next($l->operand)->map(function($result) {
          list($unary, $operand) = $result;

          if ( ! is_null( $unary ) ) {
            switch ($unary) {
              case 'not':
              case '!':
                return P::token('op', [ 'not', $operand ] );
              case '-':
                return P::token('op', [ 'multiply', [ $operand, -1 ] ] );
              default:
                break;
            }
          }

          return $operand;
        });
      },

      'operation' => function($l) {
        return $l->operandWithUnaryOperation
        ->next(
          $l->binaryOperator->next($l->operandWithUnaryOperation)->repeat()->flatten()
        )->flatten()
        ->map(function( $result ) {
          return count($result) <= 0 ? $result : $this->orderOperations->run( $result, function($ordered) {
            list ($a, $op, $b) = $ordered;
            return P::token('op', [ isset(self::$binaryOps[$op]) ? self::$binaryOps[$op] : 'noop', [ $a, $b ] ]);
          });
        });
      },

      'expression' => function($l) {
        return $l->nsx->or($l->operation)
          // Continue expression into a ternary branch
          ->maybeNext(P::symbol('?')->then($l->expression)->skip(P::symbol(':'))->next($l->expression))->flatten()
          ->map(function($result) {
            if (!empty($result[1])) {
              return P::token('branch', [ $result[0], $result[1][0], $result[1][1]]);
            }
            return $result[0];
          });
      },

      'preBlockExpression' => function($l) {
        return P::noop()->notFollowedBy(P::openBrace())->then($l->expression);
      },

      'variable' => function($l) {
        return P::ws(P::str('$')->then(P::word()));
      },

      'assign' => function($l) {
        return $l->variable->skip(P::colon())->next($l->expression);
      },

      'arguments' => function($l) {
        return P::betweenParensSepByComma(
          $l->variable->next(P::colon()->then($l->expression)->token('argumentValue')->otherwise(null)
        ))->map(function($result) {
          // organize call arguments
          return $result;
        })->token('args');
      },

      'argumentsShort' => function($l) {
        return $l->arguments->or($l->variable->token('args'));
      },


      'block' => function($l) {
        return P::betweenBraces($l->statement->skip(P::laxBraceTerminate())->repeat())->token('block');
      },

      'blockFn' => function($l) {
        return $l->block->flag('fn');
      },

      'blockIf' => function($l) {
        return $l->block->flag('if');
      },

      'blockLoop' => function($l) {
        return $l->block->flag('loop');
      },

      'access' => function($l) {
        return P::any(
          P::lookahead('('),
          P::lookahead('.'),
          P::lookahead('['),
          P::noop()
        )->chain(function($result) use ($l){

          if ($result === '(') {
            return P::betweenParensSepByCommaStrict(
              $l->assign->token('keywordArgument')->or($l->expression)
            )->token('call')->merge($l->access->flatten());
          }

          if ($result === '[') {
            return P::betweenBracketsStrict($l->expression)->token('access')->merge($l->access);
          }

          if ($result === '.') {
            return P::dot()->then(P::wsWord())->token('access')->merge($l->access);
          }

        })->otherwise([]);
      },

      'operand' => function($l) {
        return P::any(// Everything in this any block must return a token so order of operations can check if there are more than 1
          $l->scalar,
          P::any(
            P::symbol('function')->then($l->arguments->then($l->blockFn)),
            $l->arguments->skip(P::arrow())->merge($l->blockFn)
          )->token('closure'),
          P::betweenBracesSepByCommaStrict(P::any(
            P::wsWord()->token('scalar'), // unquoted string key access
            $l->scalar,
            P::betweenBrackets($l->expression)
          )->skip(P::colon())->next($l->expression))->token('object'),
          P::any(
            P::betweenBracketsSepByCommaStrict($l->expression)->token('array'),
            P::betweenParensStrict($l->expression),
            $l->variable->token('var'),
            P::wsWord()->token('access')
          )->merge($l->access)->map(function($result){

            return P::token('invoke', $result);
            // return P::token('invoke', $result);
          })
        );
      },

      'keyword' => function($l) {
        return P::wsWord()->skip(P::lookahead(P::whitespace1()))->chain(function($kw) {
          $rule = "keyword-$kw";
          return isset($this->language->{$rule}) ? $this->language->{$rule} : null;
        });
      },

      'statement' => function($l) {
        return P::any(
          $l->keyword,
          $l->assign->map(function($result) {
            list($word, $content) = $result;
            return P::token('define', [ $word, $content ]);
          }),
          $l->expression
        );
      },

      'keyword-break' => P::noop()->validate(function($result,$state){
        if (! in_array($state->flag(), [ 'loop', 'if' ] )) {
          return "break may only be used inside loops and if/else statements";
        }
        return true;
      })->asToken('break'),

      'keyword-continue' => P::noop()->validate(function($result,$state){
        if ($state->flag() !== 'loop') {
          return "continue may only be used inside loops";
        }
        return true;
      })->asToken('continue'),

      'keyword-return' => function($l) {
        return P::noop()->then($l->expression)->otherwise(null)->validate(function($result,$state){
          if ($state->flag() !== 'fn') {
            return "return may only be used inside functions";
          }
          return true;
        })->token('return');
      },

      'keyword-if' => function($l) {
        return $l->preBlockExpression->next($l->blockIf)->token('branch-if')
          ->merge(P::wsWord('elseif')->then($l->preBlockExpression)->next($l->blockIf)->token('branch-if')->repeat())
          ->merge(P::wsWord('else')->then($l->blockIf)->otherwise([])->token('branch-else'))
          ->flatten()
          ->map(function($terms){
            $result = null;
            $else = array_pop($terms)->content();
            while ($next = array_pop($terms)) {
              list ($condition, $block) = $next->content();
              $result = P::token('branch', [$condition, $block, empty($else) ? null : $else]);
              $else = $result;
            }
            return $result;
          });
      },

      'keyword-function' => function($l) {
        return P::wsWord()
          ->next($l->arguments->next($l->blockFn)->token('closure'))
          ->map(function($result) {
            list ($name, $fn) = $result;
            return P::token('define', [ $name, $fn ]);
          });
      },

      'keyword-while' => function($l) {
        return P::noop()->then($l->preBlockExpression)->next($l->blockLoop)->token('while');
      },

      'keyword-until' => function($l) {
        return P::noop()->then($l->preBlockExpression)->next($l->blockLoop)->token('until');
      },

      'keyword-each' => function($l) {
        return P::noop()
          ->then($l->preBlockExpression)
          ->skip(P::symbol('as'))
          ->next(
            $l->variable->or(P::betweenBrackets($l->variable))
          )->flatten()
          ->merge($l->blockLoop)->token('each');
      },

      'keyword-do' => function($l) {
        return P::noop()->then($l->blockLoop)->skip(P::symbol('while'))->next($l->preBlockExpression)->reverse()->token('do-while');
      }
    ];
  }

}
