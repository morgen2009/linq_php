<?php

namespace Qmaker\Lambda;


use Qmaker\Fixtures\Car;
use Qmaker\Fixtures\CarExample;

class LambdaTest extends \PHPUnit_Framework_TestCase {
    public function testVariable()
    {
        $f1 = function ($x) { return $x + $x*2 + 3; };
        $f2 = Lambda::init()->x()->add()->x()->mult(2)->add()->c(3);
        $this->assertEquals($f1(1), $f2(1));
    }

    public function testLogicalOperation()
    {
        $f1 = function ($x) { return $x > 1 && $x < 2; };
        $f2 = Lambda::init()->with()->x()->gt(1)->and_()->x()->lt(2)->end()->eq(true);
        $this->assertEquals($f1(1.5), $f2(1.5));
    }

    public function testPathOperation()
    {
        $cars = CarExample::cars();
        $f1 = function (Car $c) { return $c->getPrice(); };
        $f2 = Lambda::init()->x()->item('price');
        $this->assertEquals($f1($cars[0]), $f2($cars[0]));
    }

    public function testComplex()
    {
        $l = Lambda::init()->complex([
            'x1' => Lambda::init()->x(),
            'x2' => Lambda::init()->x()->mult(2),
            'x3' => Lambda::init()->c(1)
        ]);
        $this->assertEquals([ 'x1'=>1.5, 'x2'=>3.0, 'x3'=>1 ], $l(1.5));
    }

    public function testLike()
    {
        $l = Lambda::init()->x()->like('hello');
        $this->assertEquals(true, $l('1 hello, 1'), 'strstr');

        $l = Lambda::init()->x()->like('%hello');
        $this->assertEquals(true, $l('ww hello'), 'regexp');
    }
}