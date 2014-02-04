<?php

namespace Qmaker\Linq\Expression;


use Qmaker\Lambda\Lambda as BaseLambda;

class Lambda extends BaseLambda {
    /**
     * Add iterator into expression
     * @return Lambda|mixed
     */
    public function i() {
        $this->x(1);
        return $this;
    }

    /**
     * Transform the current value into IEnumerable
     * @return LinqLambda
     */
    public function linq() {
        return new LinqLambda($this);
    }

    /**
     * @see Qmaker\Lambda\Lambda::__invoke
     */
    function __invoke($value, \Iterator $iterator = null)
    {
        return parent::__invoke($value, $iterator);
    }
}