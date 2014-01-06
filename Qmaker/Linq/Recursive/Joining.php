<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Linq\Expression\IteratorPathExpression;
use Qmaker\Linq\Iterators\GroupingIterator;
use Qmaker\Linq\Iterators\IndexIterator;
use Qmaker\Linq\Iterators\JoinIterator;
use Qmaker\Linq\Expression\Exp;
use Qmaker\Linq\Iterators\Key\SingleKey;
use Qmaker\Linq\Iterators\Key\Storage;
use Qmaker\Linq\Iterators\MultiCallbackFilterIterator;
use Qmaker\Linq\Iterators\ProductIterator;
use Qmaker\Linq\Iterators\ProjectionIterator;
use Qmaker\Linq\Meta\Stream;

/**
 * @see \Qmaker\Linq\Operation\Joining
 */
trait Joining
{
    /**
     * @see \Qmaker\Linq\Operation\Joining::product
     */
    function product($source, callable $projector = null) {
        /** @var Stream $streamLeft */
        $streamLeft = $this->meta->getCurrent();
        /** @var Stream $streamRight */
        $streamRight = $this->importSource($source);

        $element = function (\Iterator $left, \Iterator $right) use ($streamLeft, $streamRight, $projector) {
                $result = null;

                if ($left instanceof ProductIterator) {
                    // merge with left iterator
                    $result = $left;
                    $result->attachIterator($right, $streamRight->getName());
                } elseif ($right instanceof ProductIterator) {
                    // merge with right iterator
                    $result = $right;
                    $result->attachIterator($left, $streamLeft->getName());
                } else {
                    // create new product iterator
                    $result = new ProductIterator();
                    $result->attachIterator($left, $streamLeft->getName());
                    $result->attachIterator($right, $streamRight->getName());
                }

                if (!empty($projector)) {
                    $result = new ProjectionIterator($result, $projector);
                }
                return $result;
            };

        /** @var \Qmaker\Linq\Meta\MetaAware $this */
        $this->meta->mergeStreams([$streamLeft, $streamRight])->addItem($element);
        return $this;
    }

    /**
     * @see \Qmaker\Linq\Operation\Joining::join
     */
    function join($source, $expression, $expressionInner, callable $projector = null, callable $predicate = null) {
        return $this->joinInternal($source, $expression, $expressionInner, $projector, $predicate, JoinIterator::INNER);
    }

    /**
     * @see \Qmaker\Linq\Operation\Joining::joinLeft
     */
    function joinLeft($source, $expression, $expressionInner, callable $projector = null, callable $predicate = null) {
        return $this->joinInternal($source, $expression, $expressionInner, $projector, $predicate, JoinIterator::OUTER);
    }

    private function joinInternal($source, $expression, $expressionInner, callable $projector = null, callable $predicate = null, $mode) {
        /** @var Stream $streamLeft */
        $streamLeft = $this->meta->getCurrent();
        /** @var Stream $streamRight */
        $streamRight = $this->importSource($source);

        /** @var callable $expression */
        $expression = Exp::instanceFrom($expression);
        /** @var callable $expressionInner */
        $expressionInner = Exp::instanceFrom($expressionInner);

        $element = function (\Iterator $left, \Iterator $right) use ($expression, $expressionInner, $streamLeft, $streamRight, $projector, $predicate, $mode) {
            $leftIndex = new Storage(Storage::WITHOUT_VALUES);
            $leftIndex->addKey(new SingleKey($expressionInner));

            $right = new IndexIterator($right);
            $right->getIndex()->addKey(new SingleKey($expression));

            $result = new JoinIterator($left, $leftIndex, $right, $mode);
            $result->setLeftName($streamLeft->getName());
            $result->setRightName($streamRight->getName());
            if (!empty($predicate)) {
                $result = new MultiCallbackFilterIterator($result, $predicate);
            }
            if (!empty($projector)) {
                $result = new ProjectionIterator($result, $projector);
            }

            return $result;
        };

        /** @var \Qmaker\Linq\Meta\MetaAware $this */
        $this->meta->mergeStreams([$streamLeft, $streamRight])->addItem($element);
        return $this;
    }

    /**
     * @see \Qmaker\Linq\Operation\Joining::groupJoin
     */
    public function groupJoin($source, $expression, $expressionInner, callable $projector = null, callable $predicate = null) {

        $this->join($source, $expression, $expressionInner, $projector, $predicate);

        $element = function (\Iterator $iterator) {
            $iterator = new GroupingIterator($iterator);

            /** @var $key callable */
            $key = new IteratorPathExpression('keys');
            $iterator->getIndex()->addKey(new SingleKey($key));

            return $iterator;
        };

        /** @var \Qmaker\Linq\Meta\MetaAware $this */
        $this->meta->getCurrent()->addItem($element);
        return $this;
   }
}