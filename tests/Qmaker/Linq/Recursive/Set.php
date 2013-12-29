<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Fixtures\CarExample;
use Qmaker\Linq\Linq;

class SetTest extends \PHPUnit_Framework_TestCase {

    public function testDistinct() {
        $a = [1, 2, 3, 4, 2, 1];
        $b = [1, 2, 3, 4];

        $result = Linq::from($a)->distinct()->toArray();
        $this->assertEquals($b, $result);
    }

    public function testDistinctWithExpression() {
        $a = CarExample::cars();
        $b = [1, 2, 3];

        $result = Linq::from($a)->distinct('category.id')->select('id')->toArray();
        $this->assertEquals($b, $result);
    }

    public function testExcept() {
        $a = [1, 2, 3, 4, 2, 1, 6];
        $b = [4, 3, 2, 5];
        $c = [1, 6];

        $result = Linq::from($a)->except($b)->toArray();
        $this->assertEquals($c, $result);
    }

    public function testIntersect() {
        $a = [1, 2, 3, 4, 2, 1, 6];
        $b = [4, 3, 2, 5];
        $c = [2, 3, 4];

        $result = Linq::from($a)->intersect($b)->toArray();
        $this->assertEquals($c, $result);
    }

    public function testUnion() {
        $a = [1, 2, 3, 6];
        $b = [4, 3, 2, 5];
        $c = [1, 2, 3, 4, 5, 6];

        $result = Linq::from($a)->union($b)->toArray();
        sort($result);
        $this->assertEquals($c, $result);
    }
}