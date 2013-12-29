<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Linq\LinqExpression;

/**
 * @see \Qmaker\Linq\Operation\Aggregation
 */
trait Aggregation
{
    /**
     * @see \Qmaker\Linq\Operation\Aggregation::aggregate
     */
    function aggregate(callable $aggregator, callable $init = null) {
        /** @var LinqExpression $this */
        return $this->apply('aggregate', [$aggregator, $init]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Aggregation::average
     */
    function average($expression = null) {
        /** @var LinqExpression $this */
        return $this->apply('average', [$expression]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Aggregation::count
     */
    function count($expression = null) {
        /** @var LinqExpression $this */
        return $this->apply('count', [$expression]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Aggregation::max
     */
    function max($expression = null) {
        /** @var LinqExpression $this */
        return $this->apply('max', [$expression]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Aggregation::min
     */
    function min($expression = null) {
        /** @var LinqExpression $this */
        return $this->apply('min', [$expression]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Aggregation::sum
     */
    function sum($expression = null) {
        /** @var LinqExpression $this */
        return $this->apply('sum', [$expression]);
    }
}