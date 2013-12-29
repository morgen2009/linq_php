<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Fixtures\CallbackConverter;
use Qmaker\Fixtures\CarExample;
use Qmaker\Fixtures\Car;
use Qmaker\Linq\Linq;

class ProjectionTest extends \PHPUnit_Framework_TestCase {
    public function testExpressionSimple() {
        $cars = CarExample::cars();

        $result = Linq::from($cars)->select('id');
        $result = iterator_to_array($result);
        $this->assertEquals([1,2,3,4], $result);
    }

    public function testExpressionScalar() {
        $cars = CarExample::cars();
        $result = Linq::from($cars)->select('category.id');
        $result = iterator_to_array($result);
        $this->assertEquals([1,2,3,1], $result);
    }

    public function testExpressionCallback() {
        $cars = CarExample::cars();
        $result = Linq::from($cars)
            ->select(function(Car $x) { return $x->getCategory()->getId(); })
        ;
        $result = iterator_to_array($result);
        $this->assertEquals([1,2,3,1], $result);
    }

    public function testExpressionArray() {
        $cars = CarExample::cars();
        $result = Linq::from($cars)
            ->select([
                'ware' => '',
                'category' => function(Car $x) { return $x->getCategory()->getId(); }
            ])
            ->select('category')
        ;
        $result = iterator_to_array($result);
        $this->assertEquals([1,2,3,1], $result);
    }

    public function testExpressionArrayWithEmptyValue() {
        $cars = CarExample::cars();
        $result = Linq::from($cars)
            ->select([
                    'ware' => '',
                    'category' => function(Car $x) { return $x->getCategory()->getId(); }
                ])
            ->select('ware.id')
        ;
        $result = iterator_to_array($result);
        $this->assertEquals([1,2,3,4], $result);
    }

    public function testCast() {
        $cars = CarExample::cars();

        $ids = [0, 1, 2, 3];
        $result = Linq::from($ids)->cast(function ($id) use ($cars) { return $cars[$id]; })->toArray();

        $this->assertEquals($cars, $result);
    }

    public function testCastWithConverter() {
        $cars = CarExample::cars();

        $converter = new CallbackConverter(function ($id) use ($cars) { return $cars[$id]; });

        $ids = [0, 1, 2, 3];
        $result = Linq::from($ids)->cast($converter)->toArray();

        $this->assertEquals($cars, $result);
    }
}