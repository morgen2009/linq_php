<?php

namespace Qmaker\Iterators;

/**
 * Class ExceptIterator
 */
class ExceptIterator extends DistinctIterator
{
    /**
     * @var \Iterator
     */
    protected $iteratorSub;

    /**
     * Constructor
     * @param \Iterator $iterator
     * @param \Iterator $iteratorSub
     * @param callable $keyExtractor
     */
    public function __construct(\Iterator $iterator, \Iterator $iteratorSub, callable $keyExtractor)
    {
        parent::__construct($iterator, $keyExtractor);
        $this->iteratorSub = $iteratorSub;
    }

    /**
     * @see \Iterator::rewind()
     */
    public function rewind()
    {
        $this->set->clear();
        $this->iteratorSub->rewind();
        while ($this->iteratorSub->valid()) {
            $key = call_user_func($this->keyExtractor, $this->iteratorSub->current(), $this->iteratorSub);
            $this->set->offsetSet($key);
            $this->iteratorSub->next();
        }

        parent::rewind();
    }
}