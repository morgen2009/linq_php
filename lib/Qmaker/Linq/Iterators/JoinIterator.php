<?php

namespace Qmaker\Linq\Iterators;
use Qmaker\Linq\Iterators\Key\Storage;

/**
 * Class JoinIterator
 *
 * Join two iterators
 *
 * @package Qmaker\Linq\Iterators
 */
class JoinIterator implements \Iterator, RelationInterface
{
    /**
     * The left iterator to be joined
     * @var \Iterator
     */
    private $iteratorA;

    /**
     * @var Storage
     */
    private $indexA;

    /**
     * The right iterator to be joined
     * @var IndexIterator
     */
    private $iteratorB;

    /**
     * The field name related to the value from the left iterator
     * @var string
     */
    private $nameA = 'left';

    /**
     * The field name related to the value from the right iterator
     * @var string
     */
    private $nameB = 'right';

    /**
     * @internal
     * @var int
     */
    private $windowFirstB;

    /**
     * @internal
     * @var int
     */
    private $windowLastB;

    /**
     * Offset of the current element in the generated sequence
     * @var int
     */
    private $position;

    /**
     * @var mixed
     */
    private $currentKey;

    /**
     * @var mixed
     */
    private $prevKey;

    /**
     * @var int
     */
    private $mode;

    const INNER = 1;
    const OUTER = 2;

    /**
     * @param \Iterator $iteratorA
     * @param Key\Storage $indexA
     * @param IndexIterator $iteratorB
     * @param int $mode
     */
    public function __construct(\Iterator $iteratorA, Storage $indexA, IndexIterator $iteratorB, $mode = self::INNER) {
        $this->iteratorA = $iteratorA;
        $this->indexA = $indexA;
        $this->iteratorB = $iteratorB;
        $this->mode = $mode;
    }

    /**
     * @param string $name
     */
    public function setLeftName($name)
    {
        $this->nameA = $name;
    }

    /**
     * @return string
     */
    public function getLeftName()
    {
        return $this->nameA;
    }

    /**
     * @param string $name
     */
    public function setRightName($name)
    {
        $this->nameB = $name;
    }

    /**
     * @return string
     */
    public function getRightName()
    {
        return $this->nameB;
    }

    /**
     * @see \Iterator::current()
     * @return array
     */
    public function current()
    {
        if ($this->mode == self::INNER) {
            return [
                $this->nameA => $this->iteratorA->current(),
                $this->nameB => $this->iteratorB->current()
            ];
        } else {
            return [
                $this->nameA => $this->iteratorA->current(),
                $this->nameB => $this->windowFirstB === false ? null : $this->iteratorB->current()
            ];
        }
    }

    /**
     * @see \Iterator::key()
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @return mixed
     */
    public function keys() {
        return $this->currentKey;
    }

    /**
     * @see \Iterator::next()
     */
    public function next()
    {
        $this->position++;

        if ($this->windowFirstB !== false) {

            $this->iteratorB->next();
            if ($this->iteratorB_outside()) {

                if (!$this->iteratorA_next()) {
                    return;
                }

                // the key in A has been changed
                if ($this->indexA->compare($this->currentKey, $this->prevKey) !== 0) {
                    $this->findJoining();
                } else {
                    // else move the right iterator to the window begin
                    $this->iteratorB->seek($this->windowFirstB);
                }
            }
        } else {
            $this->iteratorA_next();
            $this->findJoining();
        }
    }

    /**
     * @see \Iterator::valid()
     * @return bool
     */
    public function valid()
    {
        return $this->iteratorA->valid();
    }

    /**
     * @see \Iterator::rewind()
     */
    public function rewind()
    {
        $this->position = 0;

        // init left iterator
        $this->iteratorA->rewind();
        $this->prevKey = null;
        $this->currentKey = $this->indexA->createKey($this->iteratorA->current());

        // init right iterator
        $this->iteratorB->rewind();

        // search first joined items
        $this->findJoining();
    }

    private function iteratorA_next() {
        $this->iteratorA->next();
        $this->prevKey = $this->currentKey;
        if ($this->iteratorA->valid()) {
            $this->currentKey = $this->indexA->createKey($this->iteratorA->current());
            return true;
        } else {
            $this->currentKey = null;
            return false;
        }
    }

    private function findJoining() {
        $this->windowFirstB = $this->iteratorB->getIndex()->searchByKey($this->currentKey);
        $this->windowLastB = false;

        while ($this->mode == self::INNER && $this->windowFirstB === false) {
            if ($this->iteratorA_next()) {
                $this->windowFirstB = $this->iteratorB->getIndex()->searchByKey($this->currentKey);
            } else {
                break;
            }
        };
    }

    private function iteratorB_outside() {
        if ($this->iteratorB->valid()) {
            if ($this->windowLastB === false) {
                if ($this->indexA->compare($this->currentKey, $this->iteratorB->keys()) !== 0) {
                    $this->windowLastB = $this->iteratorB->key() - 1;
                    return true;
                }
            } else {
                return $this->iteratorB->key() > $this->windowLastB;
            }
        } else {
            return true;
        }
        return false;
    }

    /**
     * @see RelationInterface::getRelatedIterators
     */
    public function getRelatedIterators()
    {
        return [ $this->nameA => $this->iteratorA, $this->nameB => $this->iteratorB ];
    }
}