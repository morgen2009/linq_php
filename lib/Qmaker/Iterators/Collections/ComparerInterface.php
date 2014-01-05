<?php

namespace Qmaker\Iterators\Collections;

interface ComparerInterface {
    /**
     * @param mixed $x
     * @param mixed $y
     * @return int 1, if $x > $y, -1, if $x < $y, 0, if $x = $y
     */
    function compare($x, $y);
}