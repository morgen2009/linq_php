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
 * @method \Qmaker\Lambda\Lambda add($a = null) '+' operator
 * @method \Qmaker\Lambda\Lambda sub($a = null) '-' operator
 * @method \Qmaker\Lambda\Lambda mult($a = null) '*' operator
 * @method \Qmaker\Lambda\Lambda div($a = null) '/' operator
 * @method \Qmaker\Lambda\Lambda eq($a = null) '==' operator
 * @method \Qmaker\Lambda\Lambda ne($a = null) '!=' operator
 * @method \Qmaker\Lambda\Lambda gt($a = null) '>' operator
 * @method \Qmaker\Lambda\Lambda ge($a = null) '>=' operator
 * @method \Qmaker\Lambda\Lambda lt($a = null) '<' operator
 * @method \Qmaker\Lambda\Lambda le($a = null) '>=' operator
 * @method \Qmaker\Lambda\Lambda and_($a = null) logical AND
 * @method \Qmaker\Lambda\Lambda or_($a = null) logical OR
 * @method \Qmaker\Lambda\Lambda xor_($a = null) logical XOR
 * @method static \Qmaker\Lambda\Lambda _complex(array $a)
 * @method static \Qmaker\Lambda\Lambda _x(int $i=0)
 * @method static \Qmaker\Lambda\Lambda _c($value)
 */
class Lambda extends Expression {

    /**
     * @return Lambda
     */
    public static function init()
    {
        return new Lambda();
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
     * @param string $expression
     * @throws \BadMethodCallException
     * @return Lambda|mixed
     */
    public function math($expression) {
        throw new \BadMethodCallException('Not implemented');
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

    /**
     * Hook for static methods. If the prefix of the method is '_', then the new instance will be created and the corresponding method (without leading "_") will be executed
     * @param $name
     * @param $arguments
     * @return Lambda|mixed
     */
    public static function __callStatic($name, $arguments)
    {
        if (substr($name, 0, 1) === '_') {
            $object = new Lambda();
            return call_user_func_array([$object, substr($name, 1)], $arguments);
        } else {
            return null;
        }
    }
}