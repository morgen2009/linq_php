<?php

namespace Qmaker\Linq\Operation;

interface Aggregation
{
    /**
     * Performs a custom aggregation operation on the values of a collection
     * @param callable(@param $iterator, @return init) $accumulate
     * @param callable(@param $item, @param $result, @return $result)|null $init
     * @return mixed
     */
    function aggregate(callable $accumulate, callable $init = null);

    /**
     * Calculates the average value of a collection of values
     * @param mixed $expression
     * @return mixed
     * @see \Qmaker\Linq\Expression\LambdaFactory::create
     */
    function average($expression = null);

    /**
     * Counts the elements in a collection
     * @param mixed $expression
     * @return mixed
     * @see \Qmaker\Linq\Expression\LambdaFactory::create
     */
    function count($expression = null);

    /**
     * Determines the maximum value in a collection
     * @param mixed $expression
     * @return mixed
     * @see \Qmaker\Linq\Expression\LambdaFactory::create
     */
    function max($expression = null);

    /**
     * Determines the minimum value in a collection
     * @param mixed $expression
     * @return mixed
     * @see \Qmaker\Linq\Expression\LambdaFactory::create
     */
    function min($expression = null);

    /**
     * Calculates the sum of the values in a collection
     * @param mixed $expression
     * @return mixed
     * @see \Qmaker\Linq\Expression\LambdaFactory::create
     */
    function sum($expression = null);
}