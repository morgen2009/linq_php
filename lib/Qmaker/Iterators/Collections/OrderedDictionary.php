<?php

namespace Qmaker\Iterators\Collections;

class OrderedDictionary implements \Iterator, \ArrayAccess, \SeekableIterator, \Countable, ComparerInterface {

    /**
     * @var array
     */
    protected $items;

    /**
     * @var callable
     */
    protected $comparator;

    /**
     * Internal position of the current item
     * @var int
     */
    protected $position;

    public function __construct(callable $comparator = null)
    {
        $this->items = [];
        $this->comparator = $comparator;
    }

    /**
     * @see \ArrayAccess::offsetExists
     */
    public function offsetExists($key)
    {
        return $this->search($key) >= 0;
    }

    /**
     * @see \ArrayAccess::offsetGet
     */
    public function offsetGet($key)
    {
        $i = $this->search($key);
        if ($i >= 0) {
            return $this->items[$i]->value;
        } else {
            return null;
        }
    }

    /**
     * @see \ArrayAccess::offsetSet
     */
    public function offsetSet($key, $value)
    {
        $i = $this->search($key);
        if ($i >= 0) {
            $this->items[$i]->key = $value;
        } else {
            $i = -($i+1);
            $this->items = array_merge(
                array_slice($this->items, 0, $i-1),
                [ new KeyValuePair($key, $value) ],
                array_slice($this->items, $i)
            );
        }
    }

    /**
     * @see \ArrayAccess::offsetUnset
     */
    public function offsetUnset($key)
    {
        $i = $this->search($key);
        if ($i >= 0) {
            unset($this->items[$i]);
        }
    }

    /**
     * @see \Iterator::current
     */
    public function current()
    {
        return $this->items[$this->position]->value;
    }

    /**
     * @see \Iterator::next
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * @see \Iterator::key
     */
    public function key()
    {
        return $this->items[$this->position]->key;
    }

    /**
     * Get position of the current item
     * @return int
     */
    public function position()
    {
        return $this->position;
    }

    /**
     * @see \Iterator::valid
     */
    public function valid()
    {
        return $this->position < count($this->items);
    }

    /**
     * @see \Iterator::rewind
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * @see \Countable::count
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Clear dictionary
     */
    public function clear() {
        $this->items = [];
    }

    /**
     * @see ComparerInterface::compare
     */
    public function compare($x, $y) {
        if (empty($this->comparator)) {
            return $x > $y ? 1 : ($x < $y ? -1 : 0);
        } else {
            return call_user_func($this->comparator, $x, $y);
        }
    }

    /**
     * @param \Iterator $iterator
     * @return void
     */
    public function load(\Iterator $iterator) {
        // load
        $beginWindow = count($this->items);
        $iterator->rewind();
        while ($iterator->valid()) {
            $this->items[] = new KeyValuePair($iterator->key(), $iterator->current());
            $iterator->next();
        }
        $endWindow = count($this->items);

        // sort
        $self = $this;
        if ($endWindow > $beginWindow) {
            usort($this->items, function ($x, $y) use ($self) {
                return $self->compare($x->key, $y->key);
            });
        }
    }

    /**
     * Get position of the given key using binary search
     * @param array|mixed $key
     * @return int The position of the key in the $this->items. If negative value, the position of the key closest to the given key
     */
    public function search($key) {
        $low = 0;
        $high = count($this->items) - 1;
        while ($low <= $high) {
            $mid = (int)(($low + $high) / 2);
            $midVal = $this->items[$mid]->key;

            switch ($this->compare($midVal, $key)) {
                case 1: {
                    $high = $mid - 1;
                    break;
                }
                case -1: {
                    $low = $mid + 1;
                    break;
                }
                default : {
                    return $mid; // key found
                }
            }
        }
        return -($low + 1);  // key not found.
    }

    /**
     * @see SeekableIterator::seek
     */
    public function seek($position)
    {
        if ($position >= 0 && $position < count($this->items)) {
            $this->position = $position;
        }
    }

}