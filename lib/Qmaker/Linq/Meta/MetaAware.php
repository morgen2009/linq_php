<?php

namespace Qmaker\Linq\Meta;

trait MetaAware
{
    /**
     * @var \Qmaker\Linq\Meta\Meta
     */
    protected $meta = null;

    /**
     * @return \Qmaker\Linq\Meta\Meta
     */
    public function getMeta() {
        if (empty($this->meta)) {
            $this->meta = new Meta();
        }
        return $this->meta;
    }
}