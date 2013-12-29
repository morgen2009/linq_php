<?php

namespace Qmaker\Fixtures;

/**
 * Class InitializeFromArray
 * @package Qmaker\Fixtures
 */
trait InitializeFromArray {

    /**
     * @param array $params
     * @return object
     */
    public static function instance($params) {
        $class = __CLASS__;
        $object = new $class();
        $object->set($params);
        return $object;
    }

    /**
     * Copy values into object from array
     * @param array $params
     */
    protected function set($params) {
        // set values using public properties
        $properties = get_object_vars($this);
        foreach ($params as $name => $value) {
            if (array_key_exists($name, $properties)) {
                $this->{$name} = $value;
            }
        }

        // set values using public setter
        $setters = get_class_methods(__CLASS__);
        foreach ($params as $name => $value) {
            if (is_array($value)) {
                $method = 'add' . ucwords($name);
                if (array_key_exists($method, $setters)) {
                    foreach ($value as $item) {
                        $this->{$method}($item);
                    }
                    continue;
                }
            }
            $method = 'set' . ucwords($name);
            if (array_key_exists($method, $setters)) {
                $this->{$method}($value);
            }
        }
    }
} 