<?php

namespace Qmaker\Linq\Operation;

interface Filtering
{
    /**
     * Selects values, depending on their ability to be cast to a specified type
     *
     * @param string $name The name of class or trait or standard type
     * @return $this
     * @throws \Qmaker\Linq\WrongTypeException
     */
    function ofType($name);

    /**
     * Selects values that are based on a predicate function
     *
     * @param callable $predicate
     * @return $this
     */
    function where($predicate);
}
