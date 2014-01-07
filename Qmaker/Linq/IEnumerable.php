<?php

namespace Qmaker\Linq;


use Qmaker\Linq\Operation\Filtering;
use Qmaker\Linq\Operation\Generation;
use Qmaker\Linq\Operation\Partitioning;

interface IEnumerable extends \IteratorAggregate, Generation, Filtering, Partitioning {

}