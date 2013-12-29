<?php

namespace Qmaker\Linq\Operation;

interface Filtering
{
    /**
     * Selects values, depending on their ability to be cast to a specified type
     *
     * @param string $typeName The name of class or trait or standard type
     * @return $this
     * @throws \Qmaker\Linq\WrongTypeException
     */
    function ofType($typeName);

    /**
     * Selects values that are based on a predicate function
     *
     * @param callable $callback
     * @return $this
     */
    function where(callable $callback);
}
