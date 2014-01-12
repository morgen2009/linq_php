<?php

namespace Qmaker\Linq\Expression;


class ExpressionBuilder {

    const DATA = 9999;

    /**
     * @var array
     */
    protected $levels;

    /**
     * Constructor
     */
    public function __construct() {
        $this->levels = [];
    }

    /**
     * Add element into expression
     * @param mixed $value
     * @param $priority
     * @return $this
     */
    public function add($value, $priority = self::DATA) {
        // find level with priority not over the given one
        $current = count($this->levels)-1;
        while ($current >= 0 && $this->levels[$current][0] > $priority) {
            $current--;
        }

        // collapse underlying levels
        if (count($this->levels) > $current + 1) {
            $this->collapse($current+1);
            $data = $this->_export($current+1);
            unset($this->levels[$current+1]);
        } else {
            $data = [];
        }

        // add data into new or existing level
        $tryToMerge = $current >= 0 &&
            $this->levels[$current][0] == $priority &&
            ($priority >= self::DATA || $this->levels[$current][2] == $value);

        if (!$tryToMerge) {
            $current++;
            if ($priority >= self::DATA) {
                $this->levels[$current] = [$priority, 0, $value];
            } else {
                $this->levels[$current] = [$priority, 1, $value];
            }
        }

        if (!empty($data)) {
            $this->levels[$current] = array_merge( $this->levels[$current], $data );
            $this->levels[$current][1]++;
        }
        return $this;
    }

    protected function collapse($offset) {
        $i = count($this->levels)-1;
        while ($i>$offset && $i>0) {
            $data = $this->_export($i);
            $this->levels[$i-1] = array_merge($this->levels[$i-1], $data);
            $this->levels[$i-1][1]++;
            unset($this->levels[$i]);
            $i--;
        }
    }

    /**
     * @param int $offset
     * @return array
     */
    protected function _export($offset) {
        $data = $this->levels[$offset];
        $priority = array_shift($data);
        $count = array_shift($data);

        if ($priority != self::DATA) {
            $operation = array_shift($data);
            $data = array_merge($data, [$count-1, $operation]);
        }

        return $data;
    }

    /**
     * Export expression in reverse polish notation
     * @return array
     */
    public function export()
    {
        $this->collapse(0);
        return $this->_export(0);
    }
}