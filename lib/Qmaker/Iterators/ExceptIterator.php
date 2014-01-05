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
    private $iteratorSub;

    /**
     * Constructor
     * @param \Iterator $iterator
     * @param \Iterator $iteratorSub
     * @param callable $keyExtractor
     */
    public function __construct(\Iterator $iterator, \Iterator $iteratorSub, callable $keyExtractor) {
        parent::__construct($iterator, $keyExtractor);
        $this->iteratorSub = $iteratorSub;
    }

    /**
     * @see \Iterator::rewind()
     */
    public function rewind()
    {
        parent::rewind();
        $this->set->clear();

        $self = $this;
        iterator_apply($this->iteratorSub, function ($value) use ($self) {
            $key = call_user_func($self->keyExtractor, $value, $self->iteratorSub);
            $self->set->offsetSet($key);
        });
    }
}