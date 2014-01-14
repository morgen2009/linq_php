<?php

namespace Qmaker\Linq\Expression\Operation;


class Callback implements OperationInterface {

    /**
     * @var callable
     */
    protected $action;

    public function __construct(callable $action) {
        $this->action = $action;
    }

    /**
     * @see OperationInterface::compute
     */
    public function compute(\SplStack $stack)
    {
        $count = $stack->pop();
        $arguments = [];
        while ($count > 0) {
            $arguments[] = $stack->pop();
            $count--;
        }
        $result = call_user_func_array($this->action, $arguments);
        $stack->push($result);
    }
}