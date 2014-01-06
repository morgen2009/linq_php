<?php

namespace Qmaker\Linq\Operation;

interface Quantifier
{
    /**
     * Determines whether all the elements in a sequence satisfy a condition
     * @param callable $expression
     */
    function all(callable $expression);

    /**
     * Determines whether any elements in a sequence satisfy a condition
     * @param callable $expression
     */
    function any(callable $expression);

    /**
     * Determines whether a sequence contains a specified element
     * @param callable $comparator
     * @param mixed $element
     */
    function contains($element, callable $comparator = null);
}