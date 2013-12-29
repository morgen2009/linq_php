<?php

namespace Qmaker\Linq\Iterators\Key;

interface KeyInterface
{
    /**
     * Compare two keys
     * @param mixed $x
     * @param mixed $y
     * @return int 1 (x>y), -1 (x<y), 0 (x=y)
     */
    public function compare($x, $y);

    /**
     * Compute key from the value
     * @param $value
     * @return mixed
     */
    public function compute($value);

    /**
     * Get reverse order
     * @return boolean
     */
    public function getReverse();

    /**
     * Set reverse order
     * @param bool $reverse
     */
    public function setReverse($reverse);
}