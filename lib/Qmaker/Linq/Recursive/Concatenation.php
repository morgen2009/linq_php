<?php

namespace Qmaker\Linq\Recursive;

/**
 * @see \Qmaker\Linq\Operation\Concatenation
 */
trait Concatenation
{
    /**
     * @see \Qmaker\Linq\Operation\Concatenation::concat
     */
    function concat($sequence) {
        if (is_array($sequence)) {
            $sequence = new \ArrayIterator($sequence);
        } elseif ($sequence instanceof \Iterator) {
        } else {
            throw new \BadMethodCallException("The sequence can be either array or iterator");
        }

        // create filtering element
        $element = function (\Iterator $iterator) use ($sequence) {
            $result = new \AppendIterator();
            $result->append($iterator);
            $result->append($sequence);
            return $result;
        };

        /** @var $this \Qmaker\Linq\Meta\MetaAware */
        $this->meta->getCurrent()->addItem($element);
        return $this;
    }
}