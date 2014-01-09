<?php

namespace Qmaker\Linq\Operation;

interface Equality
{
    /**
     * Determines whether two sequences are equal by comparing elements in a pair-wise manner
     * @param \Iterator|array|callable|\Qmaker\Linq\IEnumerable $source
     * @param callable|null $comparator
     */
    function isEqual($source, callable $comparator = null);
}