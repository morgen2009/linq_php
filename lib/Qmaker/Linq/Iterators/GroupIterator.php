<?php

namespace Qmaker\Linq\Iterators;

class GroupIterator extends \ArrayIterator implements RelationInterface
{
    /**
     * @var GroupingIterator
     */
    protected $parent = null;

    /**
     * @param GroupingIterator $parent
     */
    public function __construct($parent) {
        $this->parent = $parent;
    }

    /**
     * @see RelationInterface::getRelatedIterators
     */
    public function getRelatedIterators()
    {
        return [ $this->parent ];
    }
}