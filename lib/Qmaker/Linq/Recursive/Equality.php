<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Linq\LinqExpression;

/**
 * @see \Qmaker\Linq\Operation\Equality
 */
trait Equality
{
    /**
     * @see \Qmaker\Linq\Operation::isEqual
     */
    function isEqual($sequence, callable $comparator = null) {
        /** @var LinqExpression $this */
        return $this->apply('isEqual', [$sequence, $comparator]);
    }
}