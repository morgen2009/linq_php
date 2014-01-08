<?php

namespace Qmaker\Linq;

class LinqQuantifierTest extends \PHPUnit_Framework_TestCase {

    public function testAnyCriteria() {
        $data = [1, 2, 5, 4, 3];
        $iterator = Linq::from($data);
        $this->assertEquals(true, $iterator->any(function ($value) {
            return $value % 2 == 0;
        }));

        $this->assertEquals(false, $iterator->any(function ($value) {
            return $value > 5;
        }));
    }

    public function testAllCriteria() {
        $data = [1, 2, 5, 4, 3];
        $iterator = Linq::from($data);

        $this->assertEquals(false, $iterator->all(function ($value) {
            return $value %2 == 0;
        }));

        $this->assertEquals(true, $iterator->all(function ($value) {
            return $value <= 5;
        }));
    }

    public function testContain() {
        $data = [1, 2, 5, 4, 3];
        $iterator = Linq::from($data);

        $this->assertEquals(true, $iterator->contains(1));
        $this->assertEquals(false, $iterator->contains(10));
        $this->assertEquals(true, $iterator->contains(1, function ($x, $y) {
            return $x == $y ? 0 : null;
        }));
    }
}