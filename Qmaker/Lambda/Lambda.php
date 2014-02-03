<?php

namespace Qmaker\Lambda;


use Qmaker\Lambda\Operators\Combine;
use Qmaker\Lambda\Operators\Parameters;
use Qmaker\Lambda\Operators\Callback;
use Qmaker\Lambda\Operators\Comparison;
use Qmaker\Lambda\Operators\Logical;
use Qmaker\Lambda\Operators\Math;
use Qmaker\Lambda\Operators\Path;

/**
 * @method \Qmaker\Lambda\Lambda|mixed add($a = null) '+' operator
 * @method \Qmaker\Lambda\Lambda|mixed sub($a = null) '-' operator
 * @method \Qmaker\Lambda\Lambda|mixed mult($a = null) '*' operator
 * @method \Qmaker\Lambda\Lambda|mixed div($a = null) '/' operator
 * @method \Qmaker\Lambda\Lambda|mixed eq($a = null) '==' operator
 * @method \Qmaker\Lambda\Lambda|mixed ne($a = null) '!=' operator
 * @method \Qmaker\Lambda\Lambda|mixed gt($a = null) '>' operator
 * @method \Qmaker\Lambda\Lambda|mixed ge($a = null) '>=' operator
 * @method \Qmaker\Lambda\Lambda|mixed lt($a = null) '<' operator
 * @method \Qmaker\Lambda\Lambda|mixed le($a = null) '>=' operator
 * @method \Qmaker\Lambda\Lambda|mixed and_($a = null) logical AND
 * @method \Qmaker\Lambda\Lambda|mixed or_($a = null) logical OR
 * @method \Qmaker\Lambda\Lambda|mixed xor_($a = null) logical XOR
 */
class Lambda extends Expression {

    /**
     * @return Lambda
     * @deprecated
     */
    public static function init()
    {
        return new Lambda();
    }

    /**
     * @see Qmaker\Lambda\Lambda::math
     */
    public static function define($names = null, $expression = null)
    {
        return (new Lambda())->math($names, $expression);
    }

    /**
     * Add i-th argument of the callable into expression
     * @param int $i
     * @return Lambda|mixed
     */
    public function x($i = 0) {
        $this->addData(new Parameters($i));
        return $this;
    }

    /**
     * Add constant into expression
     * @param $value
     * @return Lambda|mixed
     */
    public function c($value) {
        $this->addData($value);
        return $this;
    }

    /**
     * Add complex object (array) into expression
     * @param array $value
     * @return Lambda|mixed
     */
    public function complex(array $value)
    {
        $this->with();
        foreach ($value as $field => $item) {
            $this->addData($item);
            $this->addData($field);
            $this->addOperator(Combine::instance());
        }
        $this->end();

        return $this;
    }

    /**
     * Add transforming operator
     * @param callable $callback
     * @return Lambda|mixed
     */
    public function call(callable $callback) {
        $this->addOperator(new Callback($callback));
        return $this;
    }

    /**
     * Add mathematical expression in the string format
     * @param string|string[] $names The name (string) or names (string[]) of input parameters for the expression
     * @param string $expression Mathematical expression in the string format. If null, then the expression is given in the $names
     * @throws \BadMethodCallException
     * @return Lambda|mixed
     */
    public function math($names, $expression = null) {
        if ($expression === null) {
            $expression = $names;
            $names = 'x';
        }
        $tokens = '((\d+|\+|-|\(|\)|\*\*|/|\*|,|\.|>=|!=|<=|=|<|>|&|\||!|\^)|\s+)';
        $elements = preg_split($tokens, $expression, 0,  PREG_SPLIT_NO_EMPTY |  PREG_SPLIT_DELIM_CAPTURE);
        $params = func_get_args();
        array_shift($params);
        array_shift($params);

        $this->with();
        foreach ($elements as $item) {
            switch ($item) {
                case '+' : $this->addOperator(Math::instance(Math::ADD)); break;
                case '-' : $this->addOperator(Math::instance(Math::SUB)); break;
                case '*' : $this->addOperator(Math::instance(Math::MULT)); break;
                case '/' : $this->addOperator(Math::instance(Math::DIV)); break;
                case '**': $this->addOperator(Math::instance(Math::POWER)); break;
                case '(' : $this->with(); break;
                case ')' : $this->end(); break;
                case ',' : $this->comma(); break;
                case '.' : $this->addOperator(new Path()); break;
                case '>=': $this->addOperator(Comparison::instance(Comparison::_GE_)); break;
                case '<=': $this->addOperator(Comparison::instance(Comparison::_LE_)); break;
                case '>' : $this->addOperator(Comparison::instance(Comparison::_GT_)); break;
                case '<' : $this->addOperator(Comparison::instance(Comparison::_LT_)); break;
                case '=' : $this->addOperator(Comparison::instance(Comparison::_EQ_)); break;
                case '!=': $this->addOperator(Comparison::instance(Comparison::_NE_)); break;
                case '&' : $this->addOperator(Logical::instance(Logical::_AND_)); break;
                case '|' : $this->addOperator(Logical::instance(Logical::_OR_)); break;
                case '^' : $this->addOperator(Logical::instance(Logical::_XOR_)); break;
                case '!' : $this->addOperator(Logical::instance(Logical::_NOT_)); break;
                case 'p' : $this->addData(function () use ($params) {
                    return $params;
                }); break;
                case 'X' : $this->addData(function () {
                    return func_get_args();
                }); break;
                default: {
                    if (is_array($names)) {
                        $offset = array_search($item, $names);
                        if ($offset !== false) {
                            $this->x($offset);
                        } else {
                            $this->addData($item);
                        }
                    } else {
                        if ($item === $names) {
                            $this->x();
                        } else {
                            $this->addData($item);
                        }
                    }
                }
            }
        }
        $this->end();
        return $this;
    }

    /**
     * Add like comparison operator
     * @param string $pattern
     * @return Lambda|mixed
     */
    public function like($pattern) {
        $isRegexp = strstr($pattern, '%') !== false;
        if ($isRegexp) {
            $pattern = str_replace('[', '\[', $pattern);
            $pattern = str_replace(']', '\]', $pattern);
            $pattern = str_replace('%', '[^.]*', $pattern);
            $pattern = str_replace('\\', '\\\\', $pattern);
            $pattern = '/^' . $pattern . '$/';

            $callback = function ($variable) use ($pattern) {
                $result = preg_match($pattern, $variable);
                return $result > 0;
            };
        } else {
            $callback = function ($variable) use ($pattern) {
                return strstr($variable, $pattern) !== false;
            };
        }
        $this->addOperator(new Callback($callback));
        return $this;
    }

    /**
     * Apply path
     * @param string $path
     * @return Lambda|mixed
     */
    public function item($path) {
        $this->addOperator(new Path());
        $this->addData($path);
        return $this;
    }

    /**
     * Compute the next element
     * @return Lambda|mixed
     */
    public function comma() {
        $this->addOperator(Combine::instance());
        return $this;
    }

    /**
     * Hook for other methods
     * @param $name
     * @param $arguments
     * @return Lambda|mixed
     * @throws \BadMethodCallException
     */
    public function __call($name, $arguments) {
        switch ($name) {
            case 'and_' : $operator = Logical::instance(Logical::_AND_); break;
            case 'or_'  : $operator = Logical::instance(Logical::_OR_);  break;
            case 'xor_' : $operator = Logical::instance(Logical::_XOR_); break;
            case 'eq'   : $operator = Comparison::instance(Comparison::_EQ_); break;
            case 'ne'   : $operator = Comparison::instance(Comparison::_NE_); break;
            case 'gt'   : $operator = Comparison::instance(Comparison::_GT_); break;
            case 'ge'   : $operator = Comparison::instance(Comparison::_GE_); break;
            case 'lt'   : $operator = Comparison::instance(Comparison::_LT_); break;
            case 'le'   : $operator = Comparison::instance(Comparison::_LE_); break;
            case 'add'  : $operator = Math::instance(Math::ADD); break;
            case 'sub'  : $operator = Math::instance(Math::SUB); break;
            case 'mult' : $operator = Math::instance(Math::MULT); break;
            case 'div'  : $operator = Math::instance(Math::DIV); break;
            case 'power': $operator = Math::instance(Math::POWER); break;
            default: {
                if (function_exists($name)) {
                    $operator = new Callback($name);
                } else {
                    return $this->item($name);
                }
            }
        }

        if (empty($arguments)) {
            $this->addOperator($operator);
        } else {
            foreach ($arguments as $item) {
                $this->addOperator($operator);
                $this->addData($item);
            }
        }
        return $this;
    }
}