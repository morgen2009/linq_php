<?php

namespace Qmaker\Linq;


use Qmaker\Linq\Operation\Aggregation;
use Qmaker\Linq\Operation\Filtering;
use Qmaker\Linq\Operation\Generation;
use Qmaker\Linq\Operation\Joining;
use Qmaker\Linq\Operation\Partitioning;
use Qmaker\Linq\Operation\Projection;
use Qmaker\Linq\Operation\Quantifier;
use Qmaker\Linq\Operation\Set;
use Qmaker\Linq\Operation\Sorting;

interface IEnumerable extends \IteratorAggregate, Aggregation, Generation, Filtering, Joining, Partitioning, Projection, Quantifier, Set, Sorting {
    function toArray();
}