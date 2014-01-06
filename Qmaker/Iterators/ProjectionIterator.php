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
     * @param \Iterator $iterator
     * @return mixed
     */
    protected $projector;

    /**
     * Constants
     */
    const KEY = 1;
    const VALUE = 2;

    /**
     * @var int
     */
    protected $mode;

    /**
     * Constructor
     * @param \Traversable $iterator
     * @param callable $projector
     * @param int $mode
     */
    public function __construct(\Traversable $iterator, callable $projector, $mode = self::VALUE)
    {
        parent::__construct($iterator);
        $this->projector = $projector;
        $this->mode = $mode;
    }

    /**
     * @see \Iterator::current
     */
    public function current()
    {
        if ($this->mode == self::VALUE) {
            return call_user_func($this->projector, parent::current(), $this);
        } else {
            return parent::current();
        }
    }

    /**
     * @see \Iterator::key
     */
    public function key()
    {
        if ($this->mode == self::KEY) {
            return call_user_func($this->projector, parent::current(), $this);
        } else {
            return parent::key();
        }
    }
}