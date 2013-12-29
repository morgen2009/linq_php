<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Fixtures\Car;
use Qmaker\Fixtures\CarExample;
use Qmaker\Linq\Linq;

class EqualityTest extends \PHPUnit_Framework_TestCase {

    public function testScalarArray() {
        $a = [1, 2, 3, 4];
        $b = [3, 4, 2, 1];
        $c = [4, 3, 1, 3];

        $result = Linq::from($a)->isEqual($b);
        $this->assertEquals(true, $result);

        $result = Linq::from($a)->isEqual($c);
        $this->assertEquals(false, $result);
    }

    public function testObjectArray() {
        $cars = CarExample::cars();
        $a = [$cars[0], $cars[1], $cars[2]];
        $b = [$cars[3], $cars[1], $cars[2]];

        $result = Linq::from($a)->isEqual($a, function (Car $x, Car $y) {
            return $x->getId() > $y->getId() ? 1 : ($x->getId() < $y->getId() ? -1 : 0);
        });
        $this->assertEquals(true, $result);

        $result = Linq::from($a)->isEqual($b, function (Car $x, Car $y) {
            return $x->getId() > $y->getId() ? 1 : ($x->getId() < $y->getId() ? -1 : 0);
        });
        $this->assertEquals(false, $result);
    }
}