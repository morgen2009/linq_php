<?php

namespace Qmaker\Iterators;

/**
 * Class ProjectionIterator
 *
 * Generate the new sequence applying the callback to the elements of the old sequence
 */
class ProjectionIterator extends \IteratorIterator
{
    /**
     * @var callback
     * @param mixed $value
     * @return mixed
     */
    protected $projector;

    /**
     * Constructor
     * @param \Traversable $iterator
     * @param callable $projector
     */
    public function __construct(\Traversable $iterator, callable $projector) {
        parent::__construct($iterator);
        $this->projector = $projector;
    }

    /**
     * @see \Iterator::current()
     */
    public function current() {
        return call_user_func($this->projector, parent::current(), $this);
    }
}