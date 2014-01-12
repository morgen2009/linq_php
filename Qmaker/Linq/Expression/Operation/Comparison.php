<?php

namespace Qmaker\Linq\Expression\Operation;


class Comparison implements OperationInterface {

    const _EQ_  = 1;
    const _NE_  = 2;
    const _GT_  = 3;
    const _GE_  = 4;
    const _LT_  = 5;
    const _LE_  = 6;

    protected $action;

    public function __construct($action) {
        $this->action = $action;
    }

    /**
     * @see OperationInterface::compute
     */
    public function compute(\SplStack $stack)
    {
        $count = $stack->pop();
        $total = true;
        $current = $stack->pop();
        while ($count > 1) {
            $prev = $current;
            $current = $stack->pop();
            switch ($this->action) {
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
        $stack->push($total);
    }
}