<?php

namespace Qmaker\Linq\Recursive\Execute;

/**
 * @see \Qmaker\Linq\Operation\Element
 */
trait Element
{
    /**
     * @see \Qmaker\Linq\Operation\Element::elementAt
     */
    function elementAt($position) {
        $i = 0;
        /** @var $this \Iterator */
        $this->rewind();
        foreach ($this as $item) {
            if ($i == $position) {
                return $item;
            }
            $i++;
        }
        throw new \OutOfRangeException();
    }

    /**
     * @see \Qmaker\Linq\Operation\Element::elementAtOrDefault
     */
    function elementAtOrDefault($position, $default = null) {
        try {
            return $this->elementAt($position);
        } catch (\OutOfRangeException $e) {
            return $default;
        }
    }

    /**
     * @see \Qmaker\Linq\Operation\Element::first
     */
    function first() {
        return $this->elementAt(0);
    }

    /**
     * @see \Qmaker\Linq\Operation\Element::firstOrDefault
     */
    function firstOrDefault($default = null) {
        return $this->elementAtOrDefault(0, $default);
    }


    /**
     * @see \Qmaker\Linq\Operation\Element::last
     */
    function last() {
        /** @var $this \Iterator */
        $this->rewind();
        if (!$this->valid()) {
            throw new \OutOfRangeException();
        }
        $last = null;
        foreach ($this as $item) {
            $last = $item;
        }
        return $last;

    }

    /**
     * @see \Qmaker\Linq\Operation\Element::lastOrDefault
     */
    function lastOrDefault($default = null) {
        try {
            return $this->last();
        } catch (\OutOfRangeException $e) {
            return $default;
        }
    }

    /**
     * @see \Qmaker\Linq\Operation\Element::single
     */
    function single() {
        return $this->elementAt(0);
    }

    /**
     * @see \Qmaker\Linq\Operation\Element::singleOrDefault
     */
    function singleOrDefault($default = null) {
        return $this->elementAtOrDefault(0, $default);
    }
}