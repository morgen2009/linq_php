<?php

namespace Qmaker\Linq\Iterators;

/**
 * Class ProjectionIterator
 *
 * Apply the callback to each element of the inner iterator
 *
 * @package Qmaker\Linq\Iterators
 */
class ProjectionIterator extends \IteratorIterator
{
    /**
     * @var callback
     * @param mixed $value
     * @return mixed
     */
    protected $expression;

    /**
     * Constructor
     * @param \Traversable $iterator
     * @param callable $expression
     */
    public function __construct(\Traversable $iterator, callable $expression) {
        parent::__construct($iterator);
        $this->expression = $expression;
    }

    /**
     * @see \IteratorIterator::current()
     */
    public function current() {
        return call_user_func($this->expression, parent::current(), $this);
    }
}