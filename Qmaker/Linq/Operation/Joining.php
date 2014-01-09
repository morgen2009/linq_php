<?php

namespace Qmaker\Linq\Operation;

use Qmaker\Linq\IEnumerable;

interface Joining
{
    /**
     * Make cross-product of two iterators
     * @param array|\Iterator|callable|IEnumerable $source
     * @param callable $projector
     * @return $this
     */
    function product($source, $projector = null);

    /**
     * Joins two sequences based on key selector functions and extracts pairs of values
     * @param array|\Iterator|callable|IEnumerable $source
     * @param callable $expression The expression to compute key from value for source
     * @param callable $expressionInner The expression to compute key from value for current stream
     * @param callable $projector
     * @param callable $predicate
     * @return $this
     */
    function join($source, $expression, $expressionInner, $projector = null, $predicate = null);

    /**
     * Joins two sequences based on key selector functions and extracts pairs of values. The pair consists of the
     * element from the left stream and the element from the right stream or null, if there is no corresponding element
     * found
     * @param array|\Iterator|callable|IEnumerable $source
     * @param callable $expression The expression to compute key from value for source
     * @param callable $expressionInner The expression to compute key from value for current stream
     * @param callable $projector
     * @param callable $predicate
     * @return $this
     */
    function joinOuter($source, $expression, $expressionInner, $projector = null, $predicate = null);

    /**
     * Joins two sequences based on key selector functions and groups the resulting matches for each element
     * @param array|\Iterator|callable|IEnumerable $source
     * @param callable $expression The expression to compute key from value for source
     * @param callable $expressionInner The expression to compute key from value for current stream
     * @param callable $projector
     * @param callable $predicate
     * @return $this
     */
    function groupJoin($source, $expression, $expressionInner, $projector = null, $predicate = null);
}