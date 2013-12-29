<?php

namespace Qmaker\Linq\Operation;

use Qmaker\Linq\Expression\ConverterTypeInterface;

interface Projection
{
    /**
     * Projects values that are based on a transform function
     * @param $expression
     * @return $this
     * @see \Qmaker\Linq\Expression\Exp::instanceFrom
     */
    function select($expression);

    /**
     * Projects sequences of values that are based on a transform function and then flattens them into one sequence
     * @param $expression
     * @return $this
     * @see \Qmaker\Linq\Expression\Exp::instanceFrom
     */
    function selectMany($expression);

    /**
     * Casts the elements of a collection to a specified type
     * @param callable|ConverterTypeInterface $converter
     * @return $this
     * @see \Qmaker\Linq\Expression\Exp::instanceFrom
     */
    function cast($converter);
}