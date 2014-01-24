<?php

namespace Qmaker\Linq;


use Qmaker\Iterators\ComplexKeyInterface;
use Qmaker\Linq\Operation\Aggregation;
use Qmaker\Linq\Operation\Concatenation;
use Qmaker\Linq\Operation\Element;
use Qmaker\Linq\Operation\Equality;
use Qmaker\Linq\Operation\Filtering;
use Qmaker\Linq\Operation\Generation;
use Qmaker\Linq\Operation\Grouping;
use Qmaker\Linq\Operation\Joining;
use Qmaker\Linq\Operation\Partitioning;
use Qmaker\Linq\Operation\Projection;
use Qmaker\Linq\Operation\Quantifier;
use Qmaker\Linq\Operation\Set;
use Qmaker\Linq\Operation\Sorting;

interface IEnumerable extends \IteratorAggregate, Aggregation, Concatenation, Element, Equality, Filtering, Generation, Grouping, Joining, Partitioning, Projection, Quantifier, Set, Sorting, ComplexKeyInterface {
    /**
     * Export data to array
     * @return array
     */
    function toArray();

    /**
     * Export data to list
     * @return IEnumerable
     */
    function toList();

    /**
     * Apply callback to each element of the sequence
     * @param callable $action if callback returns false, the iteration stops
     * @return boolean false, if the iteration breaks
     */
    public function each(callable $action);
}