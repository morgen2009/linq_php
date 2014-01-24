<?php

namespace Qmaker\Iterators;


class ComplexKeyFinder {
    /**
     * @var \SplQueue
     */
    private static $stack = null;

    /**
     * @param \Iterator $iterator
     * @return null|ComplexKeyInterface
     */
    public static function findComplexKeyHolder(\Iterator $iterator)
    {
        if (empty(self::$stack)) {
            self::$stack = new \SplQueue();
        }
        self::$stack->setIteratorMode(\SplQueue::IT_MODE_DELETE | \SplQueue::IT_MODE_FIFO);
        self::$stack->push($iterator);

        while (!self::$stack->isEmpty()) {
            $iterator = self::$stack->pop();
            if ($iterator instanceof ComplexKeyInterface) {
                return $iterator;
            }
            if ($iterator instanceof \OuterIterator) {
                self::$stack->push($iterator->getInnerIterator());
            } elseif ($iterator instanceof RelationInterface) {
                foreach ($iterator->getRelatedIterators() as $item) {
                    self::$stack->push($item);
                }
            }
        }

        return null;
    }
} 