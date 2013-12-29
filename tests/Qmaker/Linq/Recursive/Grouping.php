<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Fixtures\Car;
use Qmaker\Fixtures\CarExample;
use Qmaker\Linq\Expression\Exp;
use Qmaker\Linq\Linq;

class GroupingTest extends \PHPUnit_Framework_TestCase {
    public function testSimple() {
        $cars = CarExample::cars();

        $result = Linq::from($cars)->groupBy('category.id')->select(Exp::group());
        $result = iterator_to_array($result);

        $this->assertEquals([1,2,3], $result);

        $result = Linq::from($cars)->groupBy(['cat' => 'category.id'])->select(Exp::group('cat'));
        $result = iterator_to_array($result);

        $this->assertEquals([1,2,3], $result);
    }

    public function testSimpleWithElement() {
        $cars = CarExample::cars();

        $result = Linq::from($cars)->alias('x')->groupBy('category.id')->select([ 'cat' => Exp::group(), 'first' => Linq::exp()->first() ]);
        $result = iterator_to_array($result);

        $this->assertEquals([
                ['cat' => 1, 'first' => $cars[0]],
                ['cat' => 2, 'first' => $cars[1]],
                ['cat' => 3, 'first' => $cars[2]]
            ], $result);
    }

    public function testSimpleWithQuantifier() {
        $cars = CarExample::cars();

        $result = Linq::from($cars)->alias('x')->groupBy('category.id')->select([ 'cat' => Exp::group(), 'cnt' => Linq::exp()->any(function (Car $x) { return $x->getPrice() > 20000; }) ]);
        $result = iterator_to_array($result);

        $this->assertEquals([
                ['cat' => 1, 'cnt' => false],
                ['cat' => 2, 'cnt' => false],
                ['cat' => 3, 'cnt' => true]
            ], $result);
    }

    public function testSimpleWithAggregation() {
        $cars = CarExample::cars();

        $result = Linq::from($cars,'x')->groupBy('category.id')->select([ 'cat' => Exp::group(), 'cnt' => Linq::exp()->count() ]);
        $result = iterator_to_array($result);

        $this->assertEquals([
                ['cat' => 1, 'cnt' => 2],
                ['cat' => 2, 'cnt' => 1],
                ['cat' => 3, 'cnt' => 1]
            ], $result);
    }
}