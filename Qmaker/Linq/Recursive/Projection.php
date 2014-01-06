<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Linq\Iterators\ProjectionIterator;
use Qmaker\Linq\Expression\Exp;
use Qmaker\Linq\Expression\ConverterTypeInterface;
use Qmaker\Linq\WrongTypeException;

/**
 * @see \Qmaker\Linq\Operation\Projection
 */
trait Projection
{
    /**
     * @see \Qmaker\Linq\Operation\Projection::select
     */
    function select($expression) {
        /** @var $expression callable */
        $expression = Exp::instanceFrom($expression);
        $element = function (\Iterator $iterator) use ($expression) {
            return new ProjectionIterator($iterator, $expression);
        };

        /** @var \Qmaker\Linq\Meta\MetaAware $this */
        $this->meta->getCurrent()->addItem($element);
        return $this;
    }

    /**
     * @see \Qmaker\Linq\Operation\Projection::select
     */
    function selectMany($expression) {
        /** @var $expression callable */
        $expression = Exp::instanceFrom($expression);

        $element = function (\Iterator $iterator) use ($expression) {
            $iterator = new ProjectionIterator($iterator, $expression);
            return new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST);
        };

        /** @var \Qmaker\Linq\Meta\MetaAware $this */
        $this->meta->getCurrent()->addItem($element);
        return $this;
    }

    /**
     * @see \Qmaker\Linq\Operation\ConvertingType::cast
     */
    function cast($converter) {
        // create callable converter
        if ($converter instanceof ConverterTypeInterface) {
            $converter = [ $converter, 'convert' ];
        } elseif (!is_callable($converter)) {
            throw new WrongTypeException($converter, 'callable|ConverterTypeInterface');
        }

        $element = function (\Iterator $iterator) use ($converter) {
            return new ProjectionIterator($iterator, $converter);
        };

        /** @var \Qmaker\Linq\Meta\MetaAware $this */
        $this->meta->getCurrent()->addItem($element);
        return $this;
    }
}