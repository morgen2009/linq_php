<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Fixtures\Car;
use Qmaker\Fixtures\CarExample;
use Qmaker\Linq\Linq;

class FilteringTest extends \PHPUnit_Framework_TestCase {

    public function testWhere() {
        $data = [1, 2, 3, 4, 5];
        $iterator = Linq::from($data)->where(function ($x) { return $x > 2; });
        $result = iterator_to_array($iterator, false);
        $this->assertEquals([3,4,5], $result);
    }

    public function testTwoWhere() {
        $data = [1, 2, 3, 4, 5];
        $iterator = Linq::from($data)->where(function ($x) { return $x > 2; })->where(function ($x) { return $x < 5; });
        $result = iterator_to_array($iterator, false);
        $this->assertEquals([3,4], $result);
    }

    public function testOfType() {
        $data = [1, 2, 3, 4, 5, "Hallo"];
        $iterator = Linq::from($data)->ofType("numeric");
        $result = iterator_to_array($iterator, false);
        $this->assertEquals([1,2,3,4,5], $result);
    }

    public function testOfTypeWithObjects() {
        $data = [1, 2, 3, 4, 5, new Car() ];
        $iterator = Linq::from($data)->ofType("\\Qmaker\\Fixtures\\Car");
        $result = iterator_to_array($iterator, false);
        $this->assertEquals(1, count($result));
    }

    public function testWhereWithMultipleParameters() {
        $data = [1, 2, 3];
        // the first "from" associated with name "a", the second - with "b"
        // "product" joins the current stream "b" with the stream "a"
        $iterator = Linq::from($data)->from($data)->product('a')->where(function ($y, $x) { return $x > $y; });
        $result = iterator_to_array($iterator, false);
        $this->assertEquals([
                ['a' => 2, 'b' => 1],
                ['a' => 3, 'b' => 1],
                ['a' => 3, 'b' => 2],
            ],$result
        );
    }

    public function testWhereWithMultipleParametersAndChangedOrderOfParameters() {
        $data = [1, 2, 3];
        // the first "from" associated with name "a", the second - with "b", the third changes the current stream to "a"
        // "product" joins the current stream "a" with the stream "b"
        $iterator = Linq::from($data)->from($data)->from('a')->product('b')->where(function ($x, $y) { return $x > $y; });
        $result = iterator_to_array($iterator, false);
        $this->assertEquals([
                ['a' => 2, 'b' => 1],
                ['a' => 3, 'b' => 1],
                ['a' => 3, 'b' => 2],
            ],$result
        );
    }

    public function testWhereWithMultipleParametersAndObject() {
        $cars = CarExample::cars();
        $result = Linq::from($cars)->alias('x')->select('category.id')->from($cars)->alias('y')->product('x')->where(function (Car $y, $x) {
            return $x == $y->getCategory()->getId();
        })->select('y.title');
        $result = iterator_to_array($result, false);
        $this->assertEquals(['Opel','Opel','BMW','Mercedes','Honda','Honda'], $result);
    }
}