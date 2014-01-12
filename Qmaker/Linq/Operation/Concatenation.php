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

    /**
     * Applies a specified function to the corresponding elements of two sequences, producing a sequence of the results.
     * @param array|callable|\Iterator|\Qmaker\Linq\IEnumerable $source
     * @param callable $projector
     * @return $this
     */
    function zip($source, callable $projector = null);
}