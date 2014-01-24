<?php

namespace Qmaker\Iterators;


interface ComplexKeyInterface {
    /**
     * Return the key of the current element
     * @return array|object|string|int
     */
    function keys();
} 