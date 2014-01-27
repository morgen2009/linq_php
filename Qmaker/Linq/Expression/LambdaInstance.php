<?php

namespace Qmaker\Linq\Expression;


use Qmaker\Lambda\Lambda as BaseLambda;

class LambdaInstance extends BaseLambda implements LambdaInterface {
    /**
     * Add value into expression
     * @param string $path
     * @return LambdaInstance|mixed
     */
    public function v($path = null) {
        $this->x(0);
        if (!empty($path)) {
            $this->item($path);
        }
        return $this;
    }

    /**
     * Add iterator into expression
     * @return LambdaInstance|mixed
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