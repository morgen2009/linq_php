<?php

namespace Qmaker\Linq\Recursive\Execute;

/**
 * @see \Qmaker\Linq\Operation\Quantifier
 */
trait Quantifier
{
    /**
     * @see \Qmaker\Linq\Operation\Quantifier:all
     */
    function all(callable $expression) {
        /** @var $this \Iterator */
        foreach ($this as $value) {
            if (!call_user_func($expression, $value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @see \Qmaker\Linq\Operation\Quantifier:any
     */
    function any(callable $expression) {
        /** @var $this \Iterator */
        foreach ($this as $value) {
            if (call_user_func($expression, $value)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @see \Qmaker\Linq\Operation\Quantifier:contains
     */
    function contains($element, callable $comparator = null) {
        /** @var $this \Iterator */
        foreach ($this as $value) {
            if (is_null($comparator)) {
                if ($value == $element) {
                    return true;
                }
            } else {
                if (call_user_func($comparator, $element, $value)) {
                    return true;
                }
            }
        }
        return false;
    }
}