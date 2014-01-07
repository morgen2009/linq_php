<?php

namespace Qmaker\Iterators\Collections;

class DefaultComparer {
    /**
     * @see ComparerInterface::compare
     */
    static function compare($x, $y) {
        return $x > $y ? 1 : ($x < $y ? -1 : 0);
    }
}