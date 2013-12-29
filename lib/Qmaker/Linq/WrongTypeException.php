<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 16.11.13
 * Time: 9:18
 */

namespace Qmaker\Linq;


class WrongTypeException extends \Exception {
    public function __construct($variable, $expectedType, $name = null) {
        if (is_object($variable)) {
            /** @var object $variable */
            $type = get_class($variable);
        } else {
            $type = gettype($variable);
        }

        if (empty($name)) {
            $name = "The parameter";
        }

        parent::__construct("{$name} ({$type}) can not be converted to {$expectedType}");
    }
} 