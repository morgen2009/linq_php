<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Linq\LinqExpression;

/**
 * @see \Qmaker\Linq\Operation\Element
 */
trait Element
{
    /**
     * @see \Qmaker\Linq\Operation\Element::elementAt
     */
    function elementAt($position) {
        /** @var LinqExpression $this */
        return $this->apply('elementAt', [$position]);
    }

    /**
     * @see \Qmaker\Linq\Operation\Element::elementAtOrDefault
     */
    function elementAtOrDefault($position, $default = null) {
        /** @var LinqExpression $this */
        return $this->apply('elementAtOrDefault', [$position, $default]);
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
        /** @var LinqExpression $this */
        return $this->apply('last', []);
    }

    /**
     * @see \Qmaker\Linq\Operation\Element::lastOrDefault
     */
    function lastOrDefault($default = null) {
        /** @var LinqExpression $this */
        return $this->apply('lastOrDefault', [$default]);
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