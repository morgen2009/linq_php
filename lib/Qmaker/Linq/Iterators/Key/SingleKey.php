<?php

namespace Qmaker\Linq\Iterators\Key;

class SingleKey implements KeyInterface
{
    /**
     * @var callable
     */
    protected $expression;

    /**
     * @var callable|null
     */
    protected $comparator;

    /**
     * @var bool
     */
    protected $reverse;

    /**
     * Constructor
     * @param callable $expression
     * @param callable $comparator
     * @param bool $reverse
     */
    public function __construct(callable $expression, callable $comparator = null, $reverse = false) {
        $this->expression = $expression;
        $this->comparator = $comparator;
        $this->reverse = $reverse;
    }

    /**
     * @see KeyInterface::setReverse
     */
    public function setReverse($reverse)
    {
        $this->reverse = $reverse;
    }

    /**
     * @see KeyInterface::getReverse
     */
    public function getReverse()
    {
        return $this->reverse;
    }

    /**
     * @param callable|null $comparator
     */
    public function setComparator(callable $comparator = null)
    {
        $this->comparator = $comparator;
    }

    /**
     * @return callable|null
     */
    public function getComparator()
    {
        return $this->comparator;
    }

    /**
     * @see KeyInterface::compare
     */
    public function compare($x, $y) {
        if (empty($this->comparator)) {
            $cmp = $x > $y ? 1 : ($x < $y ? -1 : 0);
        } else {
            $cmp = call_user_func($this->comparator, $x, $y);
        }
        return $this->reverse ? -$cmp : $cmp;
    }

    /**
     * @see KeyInterface::compute
     */
    public function compute($value) {
        return call_user_func($this->expression, $value);
    }
}