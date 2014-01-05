<?php

namespace Qmaker\Iterators\Collections;

class Dictionary implements \Iterator, \ArrayAccess, \Countable {

    /**
     * @var array
     */
    protected $items;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->items = [];
    }

    /**
     * @param array|int|string|object $key
     * @return mixed
     */
    protected function convertKeyToOffset($key)
    {
        if (is_numeric($key) || is_string($key)) {
            return $key;
        } elseif (is_array($key)) {
            return serialize($key);
        } else {
            return spl_object_hash($key);
        }
    }

    /**
     * @see \ArrayAccess::offsetExists
     */
    public function offsetExists($key)
    {
        $offset = $this->convertKeyToOffset($key);
        return isset($this->items[$offset]) || array_key_exists($offset, $this->items);
    }

    /**
     * @see \ArrayAccess::offsetGet
     */
    public function offsetGet($key)
    {
        $offset = $this->convertKeyToOffset($key);
        if ($offset === $key) {
            return $this->items[$offset];
        } else {
            return $this->items[$offset]->key;
        }
    }

    /**
     * @see \ArrayAccess::offsetSet
     */
    public function offsetSet($key, $value)
    {
        $offset = $this->convertKeyToOffset($key);
        if ($offset === $key) {
            $this->items[$offset] = $value;
        } else {
            $this->items[$offset] = new KeyValuePair($key, $value);
        }
    }

    /**
     * @see \ArrayAccess::offsetUnset
     */
    public function offsetUnset($key)
    {
        $offset = $this->convertKeyToOffset($key);
        unset($this->items[$offset]);
    }

    /**
     * @see \Iterator::current
     */
    public function current()
    {
        $current = current($this->items);
        return $current instanceof KeyValuePair ? $current->value : $current;
    }

    /**
     * @see \Iterator::next
     */
    public function next()
    {
        next($this->items);
    }

    /**
     * @see \Iterator::key
     */
    public function key()
    {
        $current = current($this->items);
        return $current instanceof KeyValuePair ? $current->key : key($this->items);
    }

    /**
     * @see \Iterator::valid
     */
    public function valid()
    {
        return key($this->items) !== null;
    }

    /**
     * @see \Iterator::rewind
     */
    public function rewind()
    {
        reset($this->items);
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
}