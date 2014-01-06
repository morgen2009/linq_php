<?php

namespace Qmaker\Linq\Recursive\Execute;

use Qmaker\Linq\Iterators\CallbackIterator;
use Qmaker\Linq\WrongTypeException;

/**
 * @see \Qmaker\Linq\Operation\Equality
 */
trait Equality
{
    /**
     * @see \Qmaker\Linq\Operation::isEqual
     */
    function isEqual($sequence, callable $comparator = null) {
        if (is_array($sequence)) {
        } elseif (is_callable($sequence)) {
            $sequence = iterator_to_array(new CallbackIterator($sequence));
        } elseif ($sequence instanceof \Iterator) {
            $sequence = iterator_to_array($sequence);
        } elseif ($sequence instanceof \IteratorAggregate) {
            $sequence = iterator_to_array($sequence->getIterator());
        } else {
            throw new WrongTypeException($sequence, "array|\\Iterator|\\IteratorAggregate|callable", "The source");
        }

        /** @var \Iterator $this */
        $iteratorA = iterator_to_array($this);
        $iteratorB = $sequence;
        if (count($iteratorA) != count($iteratorB)) {
            return false;
        }

        if (empty($comparator)) {
            sort($iteratorA);
            sort($iteratorB);
        } else {
            usort($iteratorA, $comparator);
            usort($iteratorB, $comparator);
        }

        $i = count($iteratorA)-1;
        while ($i >= 0) {
            if (empty($comparator)) {
                if ($iteratorA[$i] != $iteratorB[$i]) {
                    return false;
                }
            } else {
                if (call_user_func($comparator, $iteratorA[$i], $iteratorB[$i]) != 0) {
                    return false;
                }
            }
            $i--;
        }

        return true;
    }
}