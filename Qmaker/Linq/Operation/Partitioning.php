<?php

namespace Qmaker\Linq\Operation;

interface Partitioning
{
    /**
     * Skips elements up to a specified position in a sequence
     * @param int $count
     * @return $this
     */
    function skip($count);

    /**
     * Skips elements based on a predicate function until an element does not satisfy the condition
     * @param mixed $predicate
     * @return $this
     */
    function skipWhile($predicate);

    /**
     * Takes elements up to a specified position in a sequence
     * @param int $count
     * @return $this
     */
    function take($count);

    /**
     * Takes elements based on a predicate function until an element does not satisfy the condition
     * @param mixed $predicate
     * @return $this
     */
    function takeWhile($predicate);
}