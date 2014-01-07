<?php

namespace Qmaker\Linq\Operation;

interface Set
{
    /**
     * Removes duplicate values from a collection
     * @param mixed $expression
     * @param callable|null $comparator
     * @return $this
     * @example [a,b,b,c,d,c] => [a,b,c,d]
     * @see \Qmaker\Linq\LambdaFactory::create
     * @see \Qmaker\Iterators\Collections\ComparerInterface::compare
     */
    function distinct($expression, callable $comparator = null);

    /**
     * Returns the set difference, which means the elements of one collection that do not appear in a second collection
     * @param \Iterator|array $sequence
     * @param mixed $expression
     * @param callable|null $comparator
     * @return $this
     * @example [a,b,c,e] except [a,c,d,e] => [b]
     * @see \Qmaker\Linq\LambdaFactory::create
     * @see \Qmaker\Iterators\Collections\ComparerInterface::compare
     */
    function except($sequence, $expression, callable $comparator = null);

    /**
     * Returns the set intersection, which means elements that appear in each of two collections
     * @param \Iterator|array $sequence
     * @param mixed $expression
     * @param callable|null $comparator
     * @return $this
     * @example [a,b,c,e] intersect [a,c,d,e] => [a,c,e]
     * @see \Qmaker\Linq\LambdaFactory::create
     * @see \Qmaker\Iterators\Collections\ComparerInterface::compare
     */
    function intersect($sequence, $expression, callable $comparator = null);

    /**
     * Returns the set union, which means unique elements that appear in either of two collections
     * @param \Iterator|array $sequence
     * @param mixed $expression
     * @param callable|null $comparator
     * @return $this
     * @example [a,b,c,e] union [a,c,d,e] => [a,b,c,d,e]
     * @see \Qmaker\Linq\Expression\Exp::instanceFrom
     * @see \Qmaker\Iterators\Collections\ComparerInterface::compare
     */
    function union($sequence, $expression, callable $comparator = null);
}