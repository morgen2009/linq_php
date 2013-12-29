<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Fixtures\CarExample;
use Qmaker\Fixtures\Car;
use Qmaker\Linq\Linq;

class SortingTest extends \PHPUnit_Framework_TestCase {

    public function testNumericReverse() {
        $a = [3, 2, 1, 4];
        $result = Linq::from($a)->reverse();
        $result = iterator_to_array($result);
        $this->assertEquals([4,1,2,3], $result);
    }

    public function testNumericOrderBy() {
        $a = [3, 2, 1, 4];

        $result = iterator_to_array(Linq::from($a)->orderBy(function ($x) { return $x; })->reverse());
        $this->assertEquals([4,3,2,1], $result, "function, reverse");

        $result = iterator_to_array(Linq::from($a)->orderBy(function ($x) { return $x; }, function ($x, $y) { return $x > $y ? -1 : 1; }));
        $this->assertEquals([4,3,2,1], $result, "function, comparator");

        $result = iterator_to_array(Linq::from($a)->order()->reverse());
        $this->assertEquals([4,3,2,1], $result, "reverse");
    }

    public function testNumericThen() {
        $a = [[3, 2], [2, 2], [2, 1], [2, 4], [3, 1]];
        $b = [[2, 1], [2, 2], [2, 4], [3, 1], [3, 2]];

        $result = Linq::from($a)->orderBy(function ($x) { return $x[0]; })->thenBy(function ($x) { return $x[1]; })->toArray();
        $this->assertEquals($b, $result, "keys are callable");

        $result = Linq::from($a)->orderBy('0')->thenBy('1')->toArray();
        $this->assertEquals($b, $result, "keys are path expression");
    }

    public function testObjectOrderBy() {
        $cars = CarExample::cars();

        $result = iterator_to_array(Linq::from($cars)->orderBy(function (Car $x) { return $x->getTitle(); })->select('id'));
        $this->assertEquals([2,4,3,1], $result, "function");

        $result = iterator_to_array(Linq::from($cars)->orderBy('title')->select('id'));
        $this->assertEquals([2,4,3,1], $result, "path expression");

        $result = iterator_to_array(Linq::from($cars)->orderBy('title')->reverse()->select('id'));
        $this->assertEquals([1,3,4,2], $result, "path expression, reverse");
    }
}