<?php

namespace Qmaker\Linq\Operation;

interface Generation
{
    /**
     * Generates a collection that contains a sequence of numbers
     *
     * @param int $start The start value
     * @param int $count The number of elements
     * @return $this
     */
    static function range($start, $count);

    /**
     * Generates a collection that contains one repeated value
     *
     * @param mixed $element The element to be repeated
     * @param int $count The number of elements
     * @return $this
     */
    static function repeat($element, $count);

    /**
     * Read collection from source
     *
     * @param array|\Iterator|callable $source The input source
     * @return $this
     */
    static function from($source);
}