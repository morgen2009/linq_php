<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Fixtures\CarExample;
use Qmaker\Fixtures\Car;
use Qmaker\Linq\Linq;

class QuantifierTest extends \PHPUnit_Framework_TestCase {

    public function testAny() {
        $a = [1, 2, 3, 4];

        $result = Linq::from($a)->any(function ($i) { return $i==3; });
        $this->assertEquals(true, $result);

        $result = Linq::from($a)->any(function ($i) { return $i==6; });
        $this->assertEquals(false, $result);
    }

    public function testAll() {
        $a = [1, 2, 3, 4];

        $result = Linq::from($a)->all(function ($i) { return $i<5; });
        $this->assertEquals(true, $result);

        $result = Linq::from($a)->all(function ($i) { return $i<4; });
        $this->assertEquals(false, $result);
    }

    public function testContains() {
        $a = [1, 2, 3, 4];

        $result = Linq::from($a)->contains(1);
        $this->assertEquals(true, $result);

        $result = Linq::from($a)->contains(5);
        $this->assertEquals(false, $result);
    }

    public function testContainsObject() {
        $cars = CarExample::cars();

        $result = Linq::from($cars)->contains($cars[1]);
        $this->assertEquals(true, $result);

        $volvo = Car::instance([ 'id' => 12, 'title' => 'Volvo', 'price' => 20000 ]);

        $result = Linq::from($cars)->contains($volvo);
        $this->assertEquals(false, $result);

        $opel = Car::instance([ 'id' => 1, 'title' => 'Opel', 'price' => 16000 ]);

        $result = Linq::from($cars)->contains($opel, function (Car $x, Car $y) { return $x->getId() == $y->getId(); });
        $this->assertEquals(true, $result);
    }
}