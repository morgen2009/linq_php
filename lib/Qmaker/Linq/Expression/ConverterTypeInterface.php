<?php

namespace Qmaker\Linq\Expression;

interface ConverterTypeInterface {
    /**
     * Convert data to other type
     * @param $data
     * @return mixed
     */
    public function convert($data);
} 