<?php

namespace Qmaker\Linq\Expression;

use Qmaker\Fixtures\Car;
use Qmaker\Fixtures\CarExample;

class ExpressionTest extends \PHPUnit_Framework_TestCase {
    public function testCallback () {
        $func = function ($x) { return $x * 2; };
        $exp = Exp::instanceFrom($func);
        $this->assertEquals(2, $exp(1), 'Single argument');
    }

    public function testComparison () {
        $func = function ($x) { return $x * 2; };
        $exp = Exp::isEqual(Exp::instanceFrom($func), 2);
        $this->assertEquals(1, $exp(1), 'Equal');
    }

    public function testPath () {
        $cars = CarExample::cars();
        $exp = Exp::instanceFrom('category.id');
        $this->assertEquals(1, $exp($cars[0]));

        $exp = Exp::instanceFrom('x.category.id');
        $this->assertEquals(1, $exp([ 'x' => $cars[0] ]));
    }

    public function testArray () {
        $cars = CarExample::cars();
        $exp = Exp::instanceFrom([
            'cat' => 'category.id',
            'price' => function (Car $car) { return $car->getPrice(); }
        ]);
        $this->assertEquals(['cat' => 1, 'price' => 16000], $exp($cars[0]));
    }
}
 