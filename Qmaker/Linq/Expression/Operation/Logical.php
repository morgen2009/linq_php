<?php

namespace Qmaker\Linq\Expression\Operation;


class Logical implements OperationInterface {

    const _AND_  = 1;
    const _OR_   = 2;
    const _XOR_  = 3;
    const _NOT_  = 4;

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
        switch ($this->operation) {
            case self::_AND_: {
                $result = true;
                while ($count > 0) {
                    $result = $stack->pop() && $result;
                    $count--;
                };
                $stack->push($result);
                break;
            }
            case self::_OR_: {
                $result = false;
                while ($count > 0) {
                    $result = $stack->pop() || $result;
                    $count--;
                };
                $stack->push($result);
                break;
            }
            case self::_XOR_: {
                $result = false;
                while ($count > 0) {
                    $result = ($stack->pop() xor $result);
                    $count--;
                };
                $stack->push($result);
                break;
            }
            case self::_NOT_: {
                $result = $stack->pop();
                while ($count > 1) {
                    $stack->pop();
                    $count--;
                };
                $stack->push(!$result);
                break;
            }
        }
    }
}