<?php

namespace Qmaker\Lambda\Operators;


use Qmaker\Lambda\OperatorInterface;

class Combine implements OperatorInterface {

    public function __construct() {
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
        array_push($stack, $result);
    }

    /**
     * @see OperatorInterface::getPriority
     */
    public function getPriority()
    {
        return 1;
    }

    /**
     * @see OperatorInterface::getMaxCount
     */
    public function getMaxCount()
    {
        return PHP_INT_MAX;
    }
}