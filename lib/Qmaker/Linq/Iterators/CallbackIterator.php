<?php

namespace Qmaker\Linq\Iterators;

/**
 * Class CallbackIterator
 *
 * Generate iterator using callbacks
 *
 * @package Qmaker\Linq\Iterators
 */
class CallbackIterator implements \Iterator
{
    /**
     * Current key
     *
     * @var int
     */
    protected $offset = 0;

    /**
     * Factory producing $iterator
     * @var callable
     */
    protected $factory;

    /**
     * Callable object used to compute the next value
     * @var callable
     */
    protected $iterator = null;

    /**
     * Current value
     * @var mixed
     */
    protected $value;

    /**
     * Constructor
     *
     * @param callable $factory
     * @example Generation of 100 Fibonacci numbers
     *      $fibonacci = function () {
     *          $f2 = 0;
     *          $f1 = 1;
     *          return function ($offset) use (&$f2, &$f1) {
     *              if ($offset > 100) {
     *                  throw new \OutOfBoundsException();
     *              } else {
     *                  if ($offset == 0) {
     *                      return $f2;
     *                  } elseif ($offset == 1) {
     *                      return $f1;
     *                  } else {
     *                      $f = $f1 + $f2;
     *                      $f2 = $f1;
     *                      $f1 = $f;
     *                      return $f;
     *                  }
     *              }
     *          }
     *      }
     *      $iterator = new CallbackIterator($factory);
     */
    public function __construct(callable $factory) {
        $this->factory = $factory;
    }

    /**
     * @see \Iterator::current()
     */
    public function current()
    {
        return $this->value;
    }

    /**
     * @see \Iterator::next()
     */
    public function next()
    {
        $this->offset++;
        try {
            $this->value = call_user_func($this->iterator, $this->offset);
        } catch (\OutOfBoundsException $e) {
            $this->iterator = null;
        }
    }

    /**
     * @see \Iterator::key()
     */
    public function key()
    {
        return $this->offset;
    }

    /**
     * @see \Iterator::valid()
     */
    public function valid()
    {
        return !empty($this->iterator);
    }

    /**
     * @see \Iterator::rewind()
     */
    public function rewind()
    {
        $this->offset = 0;
        $this->iterator = call_user_func($this->factory);
        $this->value = call_user_func($this->iterator, $this->offset);
    }
}