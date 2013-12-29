<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Linq\Expression\Exp;
use Qmaker\Linq\Iterators\GroupingIterator;
use Qmaker\Linq\Iterators\Key\SingleKey;

trait Grouping
{
    /**
     * @see \Qmaker\Linq\Operation\Grouping::groupBy
     */
    function groupBy($expression, callable $comparator = null) {
        $expression = Exp::instanceFrom($expression);

        $element = function (\Iterator $iterator) use ($expression, $comparator) {
                $iterator = new GroupingIterator($iterator);
                /** @var callable $expression */
                $iterator->getIndex()->addKey(new SingleKey($expression, $comparator));
                return $iterator;
            };

        /** @var \Qmaker\Linq\Meta\MetaAware $this */
        $this->meta->getCurrent()->addItem($element);
        return $this;
    }
}