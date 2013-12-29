<?php

namespace Qmaker\Linq\Iterators\Key;

class ComplexKey implements KeyInterface
{
    /**
     * Keys
     * @var KeyInterface[]
     */
    protected $items = [];

    /**
     * @var bool
     */
    protected $reverse = false;

    public function __construct() {

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
     * @return KeyInterface[]
     */
    public function getItems() {
        return $this->items;
    }

    /**
     * @param KeyInterface $item
     */
    public function add(KeyInterface $item) {
        $this->items[] = $item;
    }

    /**
     * @see KeyInterface::compare
     */
    public function compare($x, $y) {
        foreach ($this->items as $i => $item) {
            $cmp = $item->compare($x[$i], $y[$i]);
            if ($cmp)  {
                return $this->reverse ? -$cmp : $cmp;
            }
        }
        return 0;
    }

    /**
     * @see KeyInterface::compute
     */
    public function compute($value) {
        return array_map(function(KeyInterface $item) use($value) {
            return $item->compute($value);
        }, $this->items);
    }
}