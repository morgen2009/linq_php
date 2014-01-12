<?php

namespace Qmaker\Linq\Expression\Operation;


interface OperationInterface {
    /**
     * Apply operation to the values stored in stack
     * @param \SplStack $stack
     * @return void
     */
    public function compute(\SplStack $stack);
} 