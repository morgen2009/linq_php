<?php

namespace Qmaker\Lambda;


interface ParameterAwareInterface {
    /**
     * @param mixed|$parameter
     * @return $this
     */
    public function addParameter($parameter);
}