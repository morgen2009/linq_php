<?php

namespace Qmaker\Linq\Operation;

interface Projection
{
    /**
     * Projects values that are based on a transform function
     * @param $expression
     * @return $this
     * @see \Qmaker\Linq\Expression\LambdaFactory::create
     */
    function select($expression);

    /**
     * Projects sequences of values that are based on a transform function and then flattens them into one sequence
     * @param $expression
     * @return $this
     * @see \Qmaker\Linq\Expression\LambdaFactory::create
     */
    function selectMany($expression);

    /**
     * Casts the elements of a collection to a specified type
     * @param string $name
     * @return $this
     */
    function cast($name);
}