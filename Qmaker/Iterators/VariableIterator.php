<?php

namespace Qmaker\Iterators;

/**
 * Class OuterIterator
 *
 * Iterate over the inner iterator, that can be changed in the run-time
 */
class VariableIterator extends LazyIterator
{
    /**
     * @var \Iterator
     */
    private $iterator = null;

    /**
     * Set inner iterator
     * @param \Iterator $iterator
     */
    public function setInnerIterator(\Iterator $iterator) {
        $this->iterator = $iterator;
        $this->clearInnerIterator();
    }

    /**
     * @see LazyIterator::build()
     */
    protected function build()
    {
        return $this->iterator;
    }
}