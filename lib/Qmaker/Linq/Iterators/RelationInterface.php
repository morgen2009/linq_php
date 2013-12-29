<?php

namespace Qmaker\Linq\Iterators;

interface RelationInterface
{
    /**
     * Get array of iterators, this iterator depends on
     * @return \Iterator[]
     */
    public function getRelatedIterators();
}