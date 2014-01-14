<?php

namespace Qmaker\Linq\Expression;


use Qmaker\Linq\Expression\Operation\Callback;
use Qmaker\Linq\Expression\Operation\Comparison;
use Qmaker\Linq\Expression\Operation\Logical;
use Qmaker\Linq\Expression\Operation\Math;
use Qmaker\Linq\Expression\Operation\Path;

/**
 * Class LambdaInstance
 *
 * @method \Qmaker\Linq\Expression\LambdaInstance add($a = null) '+' operator
 * @method \Qmaker\Linq\Expression\LambdaInstance sub($a = null) '-' operator
 * @method \Qmaker\Linq\Expression\LambdaInstance mult($a = null) '*' operator
 * @method \Qmaker\Linq\Expression\LambdaInstance div($a = null) '/' operator
 * @method \Qmaker\Linq\Expression\LambdaInstance eq($a = null) '==' operator
 * @method \Qmaker\Linq\Expression\LambdaInstance ne($a = null) '!=' operator
 * @method \Qmaker\Linq\Expression\LambdaInstance gt($a = null) '>' operator
 * @method \Qmaker\Linq\Expression\LambdaInstance ge($a = null) '>=' operator
 * @method \Qmaker\Linq\Expression\LambdaInstance lt($a = null) '<' operator
 * @method \Qmaker\Linq\Expression\LambdaInstance le($a = null) '>=' operator
 * @method \Qmaker\Linq\Expression\LambdaInstance and_($a = null) logical AND
 * @method \Qmaker\Linq\Expression\LambdaInstance or_($a = null) logical OR
 * @method \Qmaker\Linq\Expression\LambdaInstance xor_($a = null) logical XOR
 */
class LambdaInstance implements LambdaInterface {
    /**
     * @var ExpressionBuilder
     */
    protected $builder;

    /**
     * @var null|Expression
     */
    protected $expression = null;

    /**
     * Constructor
     */
    public function __construct() {
        $this->builder = new ExpressionBuilder();
    }

    /**
     * Add current value into expression
     * @return LambdaInstance
     */
    public function v() {
        $this->builder->add(function ($item, \Iterator $iterator = null) {
            return $item;
        });
        return $this;
    }

    /**
     * Add iterator into expression
     * @return LambdaInstance
     */
    public function i() {
        $this->builder->add(function ($item, \Iterator $iterator = null) {
            return $iterator;
        });
        return $this;
    }

    /**
     * Add value into expression
     * @param $value
     * @return LambdaInstance
     */
    public function c($value) {
        $this->builder->add($value);
        return $this;
    }

    /**
     * Add complex object (array) into expression
     * @param LambdaInterface[] $value
     * @return LambdaInstance
     */
    public function complex(array $value)
    {
        $this->builder->add(function () use ($value) {
            $result = [];
            foreach ($value as $k => $v) {
                /** @var callable $v */
                $result[$k] = call_user_func_array($v, func_get_args());
            }
            return $result;
        });
        return $this;
    }

    /**
     * Add transforming operator for the current value
     * @param callable $callback
     * @return LambdaInstance
     */
    public function call(callable $callback) {
        $this->builder->add(new Callback($callback), 10);
        return $this;
    }

    /**
     * Add transforming operator of the current value to IEnumerable
     * @throws \BadMethodCallException
     * @return \Qmaker\Linq\IEnumerable
     */
    public function linq() {
        throw new \BadMethodCallException('Not implemented');
    }

    /**
     * Add transforming operator of the current value to IEnumerable
     * @param string $expression
     * @throws \BadMethodCallException
     * @return LambdaInstance
     */
    public function math($expression) {
        throw new \BadMethodCallException('Not implemented');
    }

    /**
     * Add like comparison operator
     * @param string $expression
     * @throws \BadMethodCallException
     * @return LambdaInstance
     */
    public function like($expression) {
        throw new \BadMethodCallException('Not implemented');
    }

    /**
     * Apply path
     * @param string $path
     * @return LambdaInstance
     */
    public function item($path) {
        $operation = $this->builder->current();
        if (!($operation instanceof Path)) {
            $this->builder->add(new Path(), 10);
            $operation = $this->builder->current();
        }
        $operation->addPath($path);
        return $this;
    }

    /**
     * Add opening bracket
     * @return LambdaInstance
     */
    public function begin() {
        $this->builder->begin();
        return $this;
    }

    /**
     * Add closing bracket
     * @return LambdaInstance
     */
    public function end() {
        $this->builder->end();
        return $this;
    }

    /**
     * Hook for other methods
     * @param $name
     * @param $arguments
     * @return LambdaInstance
     * @throws \BadMethodCallException
     */
    public function __call($name, $arguments) {
        switch ($name) {
            case 'and_' : $operation = new Logical(Logical::_AND_); $priority = 3; break;
            case 'or_'  : $operation = new Logical(Logical::_OR_);  $priority = 1; break;
            case 'xor_' : $operation = new Logical(Logical::_XOR_); $priority = 2; break;
            case 'eq'   : $operation = new Comparison(Comparison::_EQ_); $priority = 4; break;
            case 'ne'   : $operation = new Comparison(Comparison::_NE_); $priority = 4; break;
            case 'gt'   : $operation = new Comparison(Comparison::_GT_); $priority = 5; break;
            case 'ge'   : $operation = new Comparison(Comparison::_GE_); $priority = 5; break;
            case 'lt'   : $operation = new Comparison(Comparison::_LT_); $priority = 5; break;
            case 'le'   : $operation = new Comparison(Comparison::_LE_); $priority = 5; break;
            case 'add'  : $operation = new Math(Math::ADD);  $priority = 6; break;
            case 'sub'  : $operation = new Math(Math::SUB);  $priority = 6; break;
            case 'mult' : $operation = new Math(Math::MULT); $priority = 7; break;
            case 'div'  : $operation = new Math(Math::DIV);  $priority = 7; break;
            default: {
                if (function_exists($name)) {
                    $operation = new Callback(function () use ($name) {
                        $arguments = func_get_args();
                        return call_user_func_array($name, $arguments);
                    });
                    $priority = 20;
                } else {
                    return $this->item($name);
                }
            }
        }

        if (empty($arguments)) {
            $this->builder->add($operation, $priority);
        } else {
            $flag = $this->builder->current() === null;
            foreach ($arguments as $item) {
                if ($flag) {
                    $flag = false;
                } else {
                    $this->builder->add($operation, $priority);
                }
                $this->builder->add($item);
            }
        }
        return $this;
    }

    /**
     * @see LambdaInterface::__invoke
     */
    function __invoke($value, \Iterator $iterator = null)
    {
        if (empty($this->expression)) {
            $this->expression = new Expression($this->builder->export());
        }
        return $this->expression->__invoke($value, $iterator);
    }
}