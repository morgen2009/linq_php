<?php

namespace Qmaker\Iterators\Collections;

class HashSet extends Dictionary {

    /**
     * Constants
     */
    const VALUE_EXISTS = null;

    /**
     * @see \ArrayAccess::offsetGet
     */
    public function offsetGet($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * @see \ArrayAccess::offsetSet
     */
    public function offsetSet($key, $value = true)
    {
        $offset = $this->convertKeyToOffset($key);

        if ($value) {
            if ($offset === $key) {
                $this->items[$offset] = &self::VALUE_EXISTS;
            } else {
                $this->items[$offset] = $key;
            }
        } else {
            unset($this->items[$offset]);
        }
    }

    /**
     * @see \Iterator::current
     */
    public function current()
    {
        $current = current($this->items);
        return $current === self::VALUE_EXISTS ? key($this->items) : $current;
    }

    /**
     * @see \Iterator::key
     */
    public function key()
    {
        return key($this->items);
    }
}