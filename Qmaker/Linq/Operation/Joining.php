<?php

namespace Qmaker\Linq\Operation;

interface Joining
{
    /**
     * Make cross-product of two iterators
     * @param array|\Iterator|callable|string $source
     * @param null|callable $projector
     * @return $this
     */
    function product($source, callable $projector = null);

    /**
     * Joins two sequences based on key selector functions and extracts pairs of values
     * @param array|\Iterator|callable|string $source
     * @param array $expression The expression to compute key from value for source
     * @param array $expressionInner The expression to compute key from value for current stream
     * @param callable $projector
     * @param callable $predicate
     * @return $this
     */
    function join($source, $expression, $expressionInner, callable $projector = null, callable $predicate = null);

    /**
     * Joins two sequences based on key selector functions and extracts pairs of values. The pair consists of the
     * element from the left stream and the element from the right stream or null, if there is no corresponding element
     * found
     * @param array|\Iterator|callable|string $source
     * @param array $expression The expression to compute key from value for source
     * @param array $expressionInner The expression to compute key from value for current stream
     * @param callable $projector
     * @param callable $predicate
     * @return $this
     */
    function joinLeft($source, $expression, $expressionInner, callable $projector = null, callable $predicate = null);

    /**
     * Joins two sequences based on key selector functions and groups the resulting matches for each element
     * @param array|\Iterator|callable|string $source
     * @param array $expression The expression to compute key from value for source
     * @param array $expressionInner The expression to compute key from value for current stream
     * @param callable $projector
     * @param callable $predicate
     * @return $this
     */
    function groupJoin($source, $expression, $expressionInner, callable $projector = null, callable $predicate = null);
}