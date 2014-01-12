<?php

namespace Qmaker\Linq\Expression\Operation;


class Math implements OperationInterface {

    const ADD   = 1;
    const SUB   = 2;
    const MULT  = 3;
    const DIV   = 4;
    const POWER = 5;

    protected $operation;

    public function __construct($operation) {
        $this->operation = $operation;
    }

    /**
     * @see OperationInterface::compute
     */
    public function compute(\SplStack $stack)
    {
        $count = $stack->pop();
        $result = $stack->pop();
        while ($count > 1) {
            switch ($this->operation) {
                case self::ADD:   $result += $stack->pop(); break;
                case self::SUB:   $result -= $stack->pop(); break;
                case self::MULT:  $result *= $stack->pop(); break;
                case self::DIV:   $result /= $stack->pop(); break;
                case self::POWER: $result = pow($result, $stack->pop()); break;
            }
            $count--;
        };
        $stack->push($result);
    }
}