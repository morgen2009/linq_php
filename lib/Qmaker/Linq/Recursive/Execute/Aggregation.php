<?php

namespace Qmaker\Linq\Recursive\Execute;

use Qmaker\Linq\Expression\Exp;

/**
 * @see \Qmaker\Linq\Operation\Aggregation
 */
trait Aggregation
{
    /**
     * @see \Qmaker\Linq\Operation\Aggregation::aggregate
     */
    function aggregate(callable $aggregator, callable $init = null) {
        /** @var $this \Iterator */
        $value = empty($init) ? 0 : call_user_func($init);
        foreach ($this as $item) {
            $value = call_user_func($aggregator, $item, $value);
        }
        return $value;
    }

    /**
     * @see \Qmaker\Linq\Operation\Aggregation::average
     */
    function average($expression = null) {
        /** @var $this \Iterator */
        $sum = 0;
        $count = 0;

        if (!empty($expression)) {
            /** @var callable $expression */
            $expression = Exp::instanceFrom($expression);
            foreach ($this as $item) {
                $sum += call_user_func($expression, $item);
                $count++;
            };
        } else {
            foreach ($this as $item) {
                $sum += $item;
                $count++;
            };
        }

        return $count > 0 ? $sum / $count : 0;
    }

    /**
     * @see \Qmaker\Linq\Operation\Aggregation::count
     */
    function count($expression = null) {
        /** @var $this \Iterator */
        if (empty($expression)) {
            return iterator_count($this);
        } else {
            /** @var callable $expression */
            $expression = Exp::instanceFrom($expression);
            $count = 0;
            foreach ($this as $item) {
                $item = call_user_func($expression, $item);
                if (!empty($item)) {
                    $count++;
                }
            };

            return $count;
        }
    }

    /**
     * @see \Qmaker\Linq\Operation\Aggregation::max
     */
    function max($expression = null) {
        /** @var $this \Iterator */
        $max = null;

        if (!empty($expression)) {
            /** @var callable $expression */
            $expression = Exp::instanceFrom($expression);
            foreach ($this as $item) {
                $item = call_user_func($expression, $item);
                if (is_null($max) || $max < $item) {
                    $max = $item;
                }
            };
        } else {
            foreach ($this as $item) {
                if (is_null($max) || $max < $item) {
                    $max = $item;
                }
            };
        }

        return $max;
    }

    /**
     * @see \Qmaker\Linq\Operation\Aggregation::min
     */
    function min($expression = null) {
        /** @var $this \Iterator */
        $min = null;

        if (!empty($expression)) {
            /** @var callable $expression */
            $expression = Exp::instanceFrom($expression);
            foreach ($this as $item) {
                $item = call_user_func($expression, $item);
                if (is_null($min) || $min > $item) {
                    $min = $item;
                }
            };
        } else {
            foreach ($this as $item) {
                if (is_null($min) || $min > $item) {
                    $min = $item;
                }
            };
        }

        return $min;
    }

    /**
     * @see \Qmaker\Linq\Operation\Aggregation::sum
     */
    function sum($expression = null) {
        /** @var $this \Iterator */
        $sum = 0;

        if (!empty($expression)) {
            /** @var callable $expression */
            $expression = Exp::instanceFrom($expression);
            foreach ($this as $item) {
               $sum += call_user_func($expression, $item);
            };
        } else {
            foreach ($this as $item) {
                $sum += $item;
            };
        }

        return $sum;
    }
}