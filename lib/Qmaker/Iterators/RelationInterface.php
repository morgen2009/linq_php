<?php

namespace Qmaker\Iterators;

interface RelationInterface
{
    /**
     * Get array of iterators, this iterator depends on
     * @return \Iterator[]
     */
    public function getRelatedIterators();
}