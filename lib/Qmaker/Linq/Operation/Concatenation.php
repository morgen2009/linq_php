<?php

namespace Qmaker\Linq\Operation;

interface Concatenation
{
    /**
     * Concatenates two sequences to form one sequence
     * @param \Iterator|array $sequence
     * @return $this
     */
    function concat($sequence);
}