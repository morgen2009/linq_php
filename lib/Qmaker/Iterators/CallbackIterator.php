<?php

namespace Qmaker\Iterators;

/**
 * Class CallbackIterator
 *
 * Generate the sequence using a callback function
 *
 * @example Generation of 100 Fibonacci numbers
 *      $fibonacci = function () {
 *          $position = 0;
 *          $f2 = 0;
 *          $f1 = 1;
 *          return function () use (&$position, &$f2, &$f1) {
 *              if ($position > 100) {
 *                  throw new \OutOfBoundsException();
 *              } else {
 *                  $position++;
 *                  if ($position == 1) {
 *                      return $f2;
 *                  } elseif ($position == 2) {
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
 *      $iterator = new CallbackIterator($fibonacci);
 */
class CallbackIterator implements \Iterator
{
    /**
     * Current key
     * @var int
     */
    protected $position;

    /**
     * Current value
     * @var mixed
     */
    protected $value;

    /**
     * Callable generating the $this->iteration
     * @var callable
     */
    protected $factory;

    /**
     * Callable computing the next item from the sequence
     * @var callable
     */
    protected $iteration;

    /**
     * Constructor
     * @param callable $factory
     */
    public function __construct(callable $factory) {
        $this->factory = $factory;
    }

    /**
     * @see \Iterator::current
     */
    public function current()
    {
        return $this->value;
    }

    /**
     * @see \Iterator::next
     */
    public function next()
    {
        $this->position++;
        try {
            $this->value = call_user_func($this->iteration, $this);
        } catch (\OutOfBoundsException $e) {
            $this->iteration = null;
        }
    }

    /**
     * @see \Iterator::key
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @see \Iterator::valid
     */
    public function valid()
    {
        return !empty($this->iteration);
    }

    /**
     * @see \Iterator::rewind
     */
    public function rewind()
    {
        $this->position = 0;
        $this->iteration = call_user_func($this->factory);
        $this->value = call_user_func($this->iteration, $this);
    }
}