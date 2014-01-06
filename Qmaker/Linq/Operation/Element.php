<?php

namespace Qmaker\Linq\Operation;

interface Element
{
    /**
     * Returns the element at a specified index in a collection
     * @param int $position
     * @throws \OutOfRangeException
     */
    function elementAt($position);

    /**
     * Returns the element at a specified index in a collection or a default value if the index is out of range
     * @param int $position
     * @param mixed $default
     * @return mixed
     * @throws \OutOfRangeException
     */
    function elementAtOrDefault($position, $default = null);

    /**
     * Returns the first element of a collection, or the first element that satisfies a condition
     * @return mixed
     */
    function first();

    /**
     * Returns the first element of a collection, or the first element that satisfies a condition. Returns a default
     * value if no such element exists
     * @param mixed $default
     * @return mixed
     */
    function firstOrDefault($default = null);

    /**
     * Returns the last element of a collection, or the last element that satisfies a condition
     * @return mixed
     */
    function last();

    /**
     * Returns the last element of a collection, or the last element that satisfies a condition. Returns a default value
     * if no such element exists
     * @param mixed $default
     * @return mixed
     */
    function lastOrDefault($default = null);

    /**
     * Returns the only element of a collection, or the only element that satisfies a condition
     * @return mixed
     */
    function single();

    /**
     * Returns the only element of a collection, or the only element that satisfies a condition. Returns a default value
     * if no such element exists or the collection does not contain exactly one element
     * @param mixed $default
     * @return mixed
     */
    function singleOrDefault($default = null);
}