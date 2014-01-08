<?php

namespace Qmaker\Linq\Operation;

interface Quantifier
{
    /**
     * Determines whether all the elements in a sequence satisfy a condition
     * @param mixed $expression
     */
    function all($expression);

    /**
     * Determines whether any elements in a sequence satisfy a condition
     * @param mixed $expression
     */
    function any($expression);

    /**
     * Determines whether a sequence contains a specified element
     * @param callable $comparator
     * @param mixed $element
     */
    function contains($element, callable $comparator = null);
}