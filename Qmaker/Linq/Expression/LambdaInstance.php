<?php

namespace Qmaker\Linq\Expression;


use Qmaker\Linq\Expression\Operation\Callback;
use Qmaker\Linq\Expression\Operation\Comparison;
use Qmaker\Linq\Expression\Operation\Logical;
use Qmaker\Linq\Expression\Operation\Math;
use Qmaker\Linq\Expression\Operation\Path;

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
     * @return $this
     */
    public function v() {
        $this->builder->add(function ($item, \Iterator $iterator = null) {
            return $item;
        });
        return $this;
    }

    /**
     * Add iterator into expression
     * @return $this
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
     * @return $this
     */
    public function c($value) {
        $this->builder->add($value);
        return $this;
    }

    /**
     * Add transforming operator for the current value
     * @param callable $callback
     * @return $this
     */
    public function call(callable $callback) {
        $this->builder->add(new Callback($callback), 1);
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
     * @return $this
     */
    public function math($expression) {
        throw new \BadMethodCallException('Not implemented');
    }

    /**
     * Add like comparison operator
     * @param string $expression
     * @throws \BadMethodCallException
     * @return $this
     */
    public function like($expression) {
        throw new \BadMethodCallException('Not implemented');
    }

    /**
     * Apply path
     * @param string $path
     * @return $this
     */
    public function item($path) {
        $operation = $this->builder->current();
        if (!($operation instanceof Path)) {
            $this->builder->add(new Path(), 1);
            $operation = $this->builder->current();
        }
        $operation->addPath($path);
        return $this;
    }

    /**
     * Add opening bracket
     * @return $this
     */
    public function begin() {
        $this->builder->begin();
        return $this;
    }

    /**
     * Add closing bracket
     * @return $this
     */
    public function end() {
        $this->builder->end();
        return $this;
    }

    /**
     * Hook for other methods
     * @param $name
     * @param $arguments
     * @return $this
     * @throws \BadMethodCallException
     */
    public function __call($name, $arguments) {
        switch ($name) {
            case '_and' : $operation = new Logical(Logical::_AND_); $priority = 1; break;
            case '_or'  : $operation = new Logical(Logical::_OR_);  $priority = 1; break;
            case '_xor' : $operation = new Logical(Logical::_XOR_); $priority = 1; break;
            case 'eq'   : $operation = new Comparison(Comparison::_EQ_); $priority = 1; break;
            case 'ne'   : $operation = new Comparison(Comparison::_NE_); $priority = 1; break;
            case 'gt'   : $operation = new Comparison(Comparison::_GT_); $priority = 1; break;
            case 'ge'   : $operation = new Comparison(Comparison::_GE_); $priority = 1; break;
            case 'lt'   : $operation = new Comparison(Comparison::_LT_); $priority = 1; break;
            case 'le'   : $operation = new Comparison(Comparison::_LE_); $priority = 1; break;
            case 'add'  : $operation = new Math(Math::ADD);  $priority = 1; break;
            case 'sub'  : $operation = new Math(Math::SUB);  $priority = 1; break;
            case 'mult' : $operation = new Math(Math::MULT); $priority = 1; break;
            case 'div'  : $operation = new Math(Math::DIV);  $priority = 1; break;
            default: {
                if (function_exists($name)) {
                    $operation = new Callback(function () use ($name) {
                        $arguments = func_get_args();
                        return call_user_func_array($name, $arguments);
                    });
                    $priority = 1;
                } else {
                    return $this->item($name);
                }
            }
        }

        if (empty($arguments)) {
            $this->builder->add($operation, $priority);
        } else {
            foreach ($arguments as $item) {
                $this->builder->add($operation, $priority);
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