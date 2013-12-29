<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Linq\Iterators\MultiCallbackFilterIterator;
use Qmaker\Linq\WrongTypeException;

/**
 * @see \Qmaker\Linq\Operation\Filtering
 */
trait Filtering
{
    /**
     * @see \Qmaker\Linq\Operation\Filtering::ofType
     */
    public function ofType($typeName)
    {
        // create filtering callback
        if (class_exists($typeName, true) || trait_exists($typeName, true)) {
            $callback = function ($item) use ($typeName) {
                return $item instanceof $typeName;
            };
        } elseif (function_exists('is_' . $typeName)) {
            $callback = function ($item) use ($typeName) {
                return call_user_func('is_' . $typeName, $item);
            };
        } else {
            throw new WrongTypeException($typeName, 'class name|trait name|type name');
        }

        // create filtering element
        $element = function (\Iterator $iterator) use ($callback) {
            if ($iterator instanceof MultiCallbackFilterIterator) {
                $iterator->addCallback($callback);
                return $iterator;
            } else {
                return new MultiCallbackFilterIterator($iterator, $callback);
            }
        };

        /** @var $this \Qmaker\Linq\Meta\MetaAware */
        $this->meta->getCurrent()->addItem($element);
        return $this;
    }

    /**
     * @see \Qmaker\Linq\Operation\Filtering::where
     */
    public function where(callable $callback)
    {
        $reflection = new \ReflectionFunction($callback);
        if ($reflection->getNumberOfParameters() > 1) {
            $callback = function ($value, \Iterator $iterator) use ($callback) {
                return call_user_func_array($callback, array_merge($value, [$iterator]));
            };
        }

        // create filtering element
        $element = function (\Iterator $iterator) use ($callback) {
            if ($iterator instanceof MultiCallbackFilterIterator) {
                $iterator->addCallback($callback);
                return $iterator;
            } else {
                return new MultiCallbackFilterIterator($iterator, $callback);
            }
        };

        /** @var $this \Qmaker\Linq\Meta\MetaAware */
        $this->meta->getCurrent()->addItem($element);
        return $this;
    }
}