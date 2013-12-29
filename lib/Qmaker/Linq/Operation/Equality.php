<?php

namespace Qmaker\Linq\Operation;

interface Equality
{
    /**
     * Determines whether two sequences are equal by comparing elements in a pair-wise manner
     * @param \Iterator|array $sequence
     * @param callable|null $comparator
     */
    function isEqual($sequence, callable $comparator = null);
}