<?php

namespace Qmaker\Linq\Expression;


use Qmaker\Lambda\Lambda as BaseLambda;

class Lambda extends BaseLambda {
    /**
     * @see Qmaker\Lambda\Lambda::define
     */
    public static function define($names = null, $expression = null)
    {
        if (empty($names)) {
            return new self();
        } else {
            return (new self())->math($names, $expression);
        }
    }

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