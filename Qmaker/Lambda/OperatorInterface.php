<?php

namespace Qmaker\Lambda;


interface OperatorInterface {
    /**
     * Get priority of the operator
     * @return int
     */
    public function getPriority();

    /**
     * Get maximal number of parameters the current operator can be applied to
     * @return int
     */
    public function getMaxCount();

    /**
     * Apply operation to the values stored in stack
     * @param array $stack
     * @return void
     */
    public function apply(array &$stack);
}