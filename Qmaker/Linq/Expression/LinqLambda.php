<?php

namespace Qmaker\Linq\Expression;

use Qmaker\Iterators\VariableIterator;
use Qmaker\Linq\Linq;

class LinqLambda extends Linq {

    /**
     * @var LambdaInstance
     */
    protected $lambda;

    /**
     * @var VariableIterator
     */
    protected $input;

    /**
     * Constructor
     * @param LambdaInstance $lambda
     */
    public function __construct(LambdaInstance $lambda) {
        $this->lambda = $lambda;
        $this->input = new VariableIterator();
        parent::__construct(function () {
            return $this->input;
        });
    }

    /**
     * @see IEnumerable::first
     * @return mixed|LambdaInstance
     */
    public function first()
    {
        return $this->apply('first');
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed|LambdaInstance
     */
    protected function apply($name, array $arguments = [])
    {
        return $this->lambda->call(function ($value) use ($name, $arguments) {
            $this->input->setInnerIterator($value);
            return $this->callParentMethod($name, $arguments);
        });
    }

    protected function callParentMethod($name, array $arguments)
    {
        return call_user_func_array(['parent', $name], $arguments);
    }
}