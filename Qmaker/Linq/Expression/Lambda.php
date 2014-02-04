<?php

namespace Qmaker\Linq\Expression;


/**
 * Class Lambda
 *
 * @method static \Qmaker\Linq\Expression\LambdaInstance|mixed v($path = null)
 * @method static \Qmaker\Linq\Expression\LambdaInstance|mixed c($value)
 * @method static \Qmaker\Linq\Expression\LambdaInstance|mixed i()
 * @method static \Qmaker\Linq\Expression\LambdaInstance|mixed with()
 * @method static \Qmaker\Linq\Expression\LambdaInstance|mixed complex(array $value)
 */
class Lambda {
    protected function __construct()
    {
    }

    static public function __callStatic($name, $arguments)
    {
        $result = new LambdaInstance();

        if (array_search($name, ['v', 'c', 'i', 'complex', 'with']) !== false || function_exists($name)) {
            return call_user_func_array([$result, $name], $arguments);
        }

        return $result->get($name);
    }
}