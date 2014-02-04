<?php

namespace Qmaker\Linq\Expression;

use Qmaker\Iterators\VariableIterator;
use Qmaker\Linq\Linq;

class LinqLambda extends Linq {

    /**
     * @var Lambda
     */
    protected $lambda;

    /**
     * @var VariableIterator
     */
    protected $input;

    /**
     * Constructor
     * @param Lambda $lambda
     */
    public function __construct(Lambda $lambda) {
        $this->lambda = $lambda;
        $this->input = new VariableIterator();
        parent::__construct(function () {
            return $this->input;
        });
    }

    /**
     * @see IEnumerable::aggregate
     * @param callable(@param $iterator, @return init) $accumulate
     * @param callable(@param $item, @param $result, @return $result)|null $init
     * @return mixed|Lambda
     */
    public function aggregate(callable $accumulate, callable $init = null)
    {
        return $this->apply('aggregate', [$accumulate, $init]);
    }

    /**
     * @see IEnumerable::average
     * @param mixed $expression
     * @return mixed|Lambda
     */
    public function average($expression = null)
    {
        return $this->apply('average', [$expression]);
    }

    /**
     * @see IEnumerable::count
     * @param mixed $expression
     * @return mixed|Lambda
     */
    public function count($expression = null)
    {
        return $this->apply('count', [$expression]);
    }

    /**
     * @see IEnumerable::max
     * @param mixed $expression
     * @return mixed|Lambda
     */
    public function max($expression = null)
    {
        return $this->apply('max', [$expression]);
    }

    /**
     * @see IEnumerable::min
     * @param mixed $expression
     * @return mixed|Lambda
     */
    public function min($expression = null)
    {
        return $this->apply('min', [$expression]);
    }

    /**
     * @see IEnumerable::sum
     * @param mixed $expression
     * @return mixed|Lambda
     */
    public function sum($expression = null)
    {
        return $this->apply('sum', [$expression]);
    }

    /**
     * @see IEnumerable::first
     * @return mixed|Lambda
     */
    public function first()
    {
        return $this->apply('first');
    }

    /**
     * @see IEnumerable::firstOrDefault
     * @param string $default
     * @return mixed|Lambda
     */
    public function firstOrDefault($default = null)
    {
        return $this->apply('firstOrDefault', [$default]);
    }

    /**
     * @see IEnumerable::last
     * @return mixed|Lambda
     */
    public function last()
    {
        return $this->apply('last');
    }

    /**
     * @see IEnumerable::lastOrDefault
     * @param string $default
     * @return mixed|Lambda
     */
    public function lastOrDefault($default = null)
    {
        return $this->apply('lastOrDefault', [$default]);
    }

    /**
     * @see IEnumerable::single
     * @return mixed|Lambda
     */
    public function single()
    {
        return $this->apply('single');
    }

    /**
     * @see IEnumerable::singleOrDefault
     * @param string $default
     * @return mixed|Lambda
     */
    public function singleOrDefault($default = null)
    {
        return $this->apply('singleOrDefault', [$default]);
    }

    /**
     * @see IEnumerable::elementAt
     * @param int $position
     * @return mixed|Lambda
     */
    public function elementAt($position)
    {
        return $this->apply('elementAt');
    }

    /**
     * @see IEnumerable::elementAtOrDefault
     * @param int $position
     * @param string $default
     * @return mixed|Lambda
     */
    public function elementAtOrDefault($position, $default = null)
    {
        return $this->apply('elementAtOrDefault', [$position, $default]);
    }

    /**
     * @see IEnumerable::isEqual
     * @param \Iterator|array|callable|\Qmaker\Linq\IEnumerable $source
     * @param callable|null $comparator
     * @return mixed|Lambda
     */
    public function isEqual($source, callable $comparator = null)
    {
        return $this->apply('isEqual', [$source, $comparator]);
    }

    /**
     * @see IEnumerable::defaultIfEmpty
     * @param mixed $default
     * @return mixed|Lambda
     */
    public function defaultIfEmpty($default = null)
    {
        return $this->apply('defaultIfEmpty', [$default]);
    }

    /**
     * @see IEnumerable::all
     * @param mixed $expression
     * @return mixed|Lambda
     */
    public function all($expression)
    {
        return $this->apply('all', [$expression]);
    }

    /**
     * @see IEnumerable::any
     * @param mixed $expression
     * @return mixed|Lambda
     */
    public function any($expression)
    {
        return $this->apply('any', [$expression]);
    }

    /**
     * @see IEnumerable::contains
     * @param callable $comparator
     * @param mixed $element
     * @return mixed|Lambda
     */
    public function contains($element, callable $comparator = null)
    {
        return $this->apply('contains', [$element, $comparator]);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed|Lambda
     */
    protected function apply($name, array $arguments = [])
    {
        return $this->lambda->apply(function ($value) use ($name, $arguments) {
            $this->input->setInnerIterator($value);
            return $this->callParentMethod($name, $arguments);
        });
    }

    protected function callParentMethod($name, array $arguments)
    {
        return call_user_func_array(['parent', $name], $arguments);
    }
}