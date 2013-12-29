<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Linq\LinqExpression;

/**
 * @see \Qmaker\Linq\Operation\Quantifier
 */
trait Quantifier
{
    /**
     * @see \Qmaker\Linq\Operation\Quantifier:all
     */
    function all(callable $expression) {
        /** @var LinqExpression $this */
            return $this->apply('all', [$expression]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Quantifier:any
     */
    function any(callable $expression) {
        /** @var LinqExpression $this */
        return $this->apply('any', [$expression]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Quantifier:contains
     */
    function contains($element, callable $comparator = null) {
        /** @var LinqExpression $this */
        return $this->apply('contains', [$element, $comparator]);
    }
}