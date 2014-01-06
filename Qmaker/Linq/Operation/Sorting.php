<?php

namespace Qmaker\Linq\Operation;

interface Sorting
{

    /**
     * Sorts values in ascending order
     * @param mixed $expression
     * @param callable|null $comparator
     * @return $this
     * @see \Qmaker\Linq\Expression\Exp::instanceFrom
     */
    function orderBy($expression, callable $comparator = null);

    /**
     * Sorts values in descending order
     * @param mixed $expression
     * @param callable|null $comparator
     * @return $this
     * @see \Qmaker\Linq\Expression\Exp::instanceFrom
     */
    function orderByDescending($expression, callable $comparator = null);

    /**
     * Performs a secondary sort in ascending order
     * @param mixed $expression
     * @param callable|null $comparator
     * @return $this
     * @see \Qmaker\Linq\Expression\Exp::instanceFrom
     */
    function thenBy($expression, callable $comparator = null);

    /**
     * Performs a secondary sort in descending order
     * @param mixed $expression
     * @param callable|null $comparator
     * @return $this
     * @see \Qmaker\Linq\Expression\Exp::instanceFrom
     */
    function thenByDescending($expression, callable $comparator = null);

    /**
     * Reverses the order of the elements in a collection
     * @return $this
     * @see \Qmaker\Linq\Expression\Exp::instanceFrom
     */
    function reverse();

    /**
     * Sorts values in ascending order
     * @param callable|null $comparator
     * @return $this
     */
    function order(callable $comparator = null);

}