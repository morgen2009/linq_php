<?php

namespace Qmaker\Lambda\Operators;


use Qmaker\Lambda\OperatorInterface;

class Comparison implements OperatorInterface {

    const _EQ_  = 1;
    const _NE_  = 2;
    const _GT_  = 3;
    const _GE_  = 4;
    const _LT_  = 5;
    const _LE_  = 6;

    protected $operator;

    protected static $instances = [];

    public function __construct($operator) {
        $this->operator = $operator;
    }

    /**
     * Create single instance for each operator
     * @param $operator
     * @return mixed
     */
    public static function instance($operator) {
        if (!isset(self::$instances[$operator])) {
            self::$instances[$operator] = new self($operator);
        }
        return self::$instances[$operator];
    }

    /**
     * @see OperatorInterface::apply
     */
    public function apply(array &$stack)
    {
        $count = array_pop($stack);
        $total = true;
        $current = array_pop($stack);
        while ($count > 1) {
            $prev = $current;
            $current = array_pop($stack);
            switch ($this->operator) {
                case self::_EQ_: $result = $prev == $current; break;
                case self::_NE_: $result = $prev != $current; break;
                case self::_GT_: $result = $prev <  $current; break;
                case self::_GE_: $result = $prev <= $current; break;
                case self::_LT_: $result = $prev >  $current; break;
                case self::_LE_: $result = $prev >= $current; break;
                default: $result = true;
            }
            $total = $total && $result;
            $count--;
        };
        array_push($stack, $total);
    }

    /**
     * @see OperatorInterface::apply
     */
    public function getPriority()
    {
        switch ($this->operator) {
            case self::_EQ_:
            case self::_NE_: return 4;
            default: return 5;
        }
    }

    /**
     * @see OperatorInterface::apply
     */
    public function getMaxCount()
    {
        return 2;
    }
}