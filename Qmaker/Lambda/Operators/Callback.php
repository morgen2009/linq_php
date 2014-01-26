<?php

namespace Qmaker\Lambda\Operators;


use Qmaker\Lambda\OperatorInterface;

class Callback implements OperatorInterface {

    /**
     * @var callable
     */
    protected $action;

    public function __construct(callable $action) {
        $this->action = $action;
    }

    /**
     * @see OperatorInterface::apply
     */
    public function apply(array &$stack)
    {
        $count = array_pop($stack);
        $arguments = [];
        while ($count > 0) {
            $arguments[] = array_pop($stack);
            $count--;
        }
        $result = call_user_func_array($this->action, $arguments);
        array_push($stack, $result);
    }

    /**
     * @see OperatorInterface::getPriority
     */
    public function getPriority()
    {
        return 20;
    }

    /**
     * @see OperatorInterface::getMaxCount
     */
    public function getMaxCount()
    {
        return PHP_INT_MAX;
    }
}