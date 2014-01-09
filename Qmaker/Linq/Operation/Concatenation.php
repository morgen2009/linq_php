<?php

namespace Qmaker\Linq\Operation;

interface Concatenation
{
    /**
     * Concatenates two sequences to form one sequence
     * @param array|callable|\Iterator|\Qmaker\Linq\IEnumerable $source
     * @return $this
     */
    function concat($source);
}