<?php

namespace Qmaker\Linq;

use Qmaker\Fixtures\Car;
use Qmaker\Fixtures\CarExample;
use Qmaker\Fixtures\Category;

class LinqJoiningTest extends \PHPUnit_Framework_TestCase {

    public function testProduct() {
        $iterator = Linq::range(1, 2)->product(Linq::range(2, 3));
        $this->assertEquals([
            [1, 2],
            [1, 3],
            [1, 4],
            [2, 2],
            [2, 3],
            [2, 4]
        ], $iterator->toArray());
    }

    public function testJoin() {
        $iterator = Linq::from(CarExample::cars())->join(CarExample::categories(), function (Category $c) {
            return $c->getId();
        }, function (Car $c) {
            return $c->getCategory()->getId();
        }, function (array $value) {
            return [
                $value[0]->getTitle(),
                $value[1]->getTitle()
            ];
        });
        $this->assertEquals([
            ['Opel', 'Low'],
            ['BMW', 'Middle'],
            ['Mercedes', 'High'],
            ['Honda', 'Low'],
        ], $iterator->toArray());
    }

    public function testJoinInverse() {
        $iterator = Linq::from(CarExample::categories())->join(CarExample::cars(), function (Car $c) {
            return $c->getCategory()->getId();
        }, function (Category $c) {
            return $c->getId();
        }, function (array $value) {
            return [
                $value[1]->getTitle(),
                $value[0]->getTitle()
            ];
        });
        $this->assertEquals([
            ['Opel', 'Low'],
            ['Honda', 'Low'],
            ['BMW', 'Middle'],
            ['Mercedes', 'High'],
        ], $iterator->toArray());
    }
}