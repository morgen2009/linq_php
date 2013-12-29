<?php

namespace Qmaker\Linq\Expression;

class DummyExpression implements ExpressionInterface
{
    /**
     * @see \Qmaker\Linq\Expression\ExpressionInterface::__invoke
     */
    function __invoke($value, \Iterator $iterator = null)
    {
        return $value;
    }
}