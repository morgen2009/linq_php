<?php

namespace Qmaker\Linq\Expression;

interface ExpressionInterface
{
    /**
     * Compute the expression.
     *
     * @param mixed $value can be specified multiple times
     * @param \Iterator $iterator
     * @return mixed
     */
    function __invoke($value, \Iterator $iterator = null);
}