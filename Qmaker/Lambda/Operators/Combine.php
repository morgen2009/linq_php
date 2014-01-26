<?php

namespace Qmaker\Lambda\Operators;


use Qmaker\Lambda\OperatorInterface;

class Combine implements OperatorInterface {

    protected static $instance = null;

    protected function __construct() {
    }

    /**
     * Create single instance
     * @return Combine
     */
    public static function instance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @see OperatorInterface::apply
     */
    public function apply(array &$stack)
    {
        $count = array_pop($stack);
        $arguments = [];
        while ($count > 0) {
            $field = array_pop($stack);
            $arguments[$field] = array_pop($stack);
            $count-=2;
        }
        array_push($stack, $arguments);
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