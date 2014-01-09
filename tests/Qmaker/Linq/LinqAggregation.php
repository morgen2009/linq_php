<?php

namespace Qmaker\Linq;

class LinqAggregationTest extends \PHPUnit_Framework_TestCase {
    protected $arrayNumeric = array (1, 2, 3, 4, 5);
    protected $arrayEmpty = array ();

    public function testMinMaxSumAvrCount() {
        $res = Linq::from($this->arrayNumeric);
        $this->assertEquals(15, $res->sum());
        $this->assertEquals(1, $res->min());
        $this->assertEquals(5, $res->max());
        $this->assertEquals(5, $res->count());
        $this->assertEquals(3, $res->average());
    }

    public function testMinMaxSumAvrCountWithEmpty() {
        $res = Linq::from($this->arrayEmpty);
        $this->assertEquals(0, $res->sum());
        $this->assertEquals(0, $res->min());
        $this->assertEquals(0, $res->max());
        $this->assertEquals(0, $res->count());
        $this->assertEquals(0, $res->average());
    }

    public function testCountWithExpression() {
        $res = Linq::from($this->arrayNumeric);
        $this->assertEquals(3, $res->count(function ($x) { return $x > 2; } ));
    }

    public function testMinMaxAvrSumWithExpression() {
        $res = Linq::from($this->arrayNumeric);
        $this->assertEquals(30, $res->sum(function ($x) { return $x * 2; }));
        $this->assertEquals(3,  $res->min(function ($x) { return $x * 3; }));
        $this->assertEquals(15, $res->max(function ($x) { return $x * 3; }));
        $this->assertEquals(11, $res->average(function ($x) { return $x * $x; }));
    }

    public function testAggregate() {
        $res = Linq::from($this->arrayNumeric)->aggregate(
            function ($value, $res) {
                return [
                    'min' => is_null($res['min']) || $res['min'] > $value ? $value : $res['min'],
                    'max' => is_null($res['max']) || $res['max'] < $value ? $value : $res['max'],
                    'count' => $res['count']+1
                ];
            },
            function () { return ['min' => null, 'max' => null, 'count' => null]; }
        );
        $this->assertEquals(['min' => 1, 'max' => 5, 'count' => 5], $res);
    }
}