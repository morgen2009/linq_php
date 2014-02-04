<?php

namespace Qmaker\Lambda;


use Qmaker\Fixtures\Car;
use Qmaker\Fixtures\CarExample;

class LambdaTest extends \PHPUnit_Framework_TestCase {
    public function testVariable()
    {
        $f1 = function ($x) { return $x + $x*2 + 3; };
        $f2 = Lambda::define()->x()->add()->x()->mult(2)->add()->c(3);
        $this->assertEquals($f1(1), $f2(1));
    }

    public function testLogicalOperation()
    {
        $f1 = function ($x) { return $x > 1 && $x < 2; };
        $f2 = Lambda::define()->with()->x()->gt(1)->and_()->x()->lt(2)->end()->eq(true);
        $this->assertEquals($f1(1.5), $f2(1.5));
    }

    public function testPathOperation()
    {
        $cars = CarExample::cars();
        $f1 = function (Car $c) { return $c->getPrice(); };
        $f2 = Lambda::define()->x()->get('price');
        $this->assertEquals($f1($cars[0]), $f2($cars[0]));
    }

    public function testComplex()
    {
        $l = Lambda::define()->complex([
            'x1' => Lambda::define()->x(),
            'x2' => Lambda::define()->x()->mult(2),
            'x3' => Lambda::define()->c(1)
        ]);
        $this->assertEquals([ 'x1'=>1.5, 'x2'=>3.0, 'x3'=>1 ], $l(1.5));
    }

    public function testLike()
    {
        $l = Lambda::define()->x()->like('hello');
        $this->assertEquals(true, $l('1 hello, 1'), 'strstr');

        $l = Lambda::define()->x()->like('%hello');
        $this->assertEquals(true, $l('ww hello'), 'regexp');
    }

    public function testMath()
    {
        $f = Lambda::define()->math('1+2*x'); // "x" stands for first argument
        $this->assertEquals(5, $f(2));

        $f = Lambda::define()->math('a', '1+2*a'); // "a" stands for first argument
        $this->assertEquals(5, $f(2));

        $f = Lambda::define()->math(['x', 'y'], 'x+2*y'); // "x" and "y" stand for first and second arguments
        $this->assertEquals(8, $f(2,3));

        $f = Lambda::define()->math('1+2*X.0>=2 & (X.1<12)'); // "X" stands for array of arguments
        $this->assertEquals(true, $f(2, 3));
    }

    public function testMathWithParams()
    {
        $f = Lambda::define()->math('1+2*x+p.0', 2); // "p" stands for array of parameters (here [2])
        $this->assertEquals(7, $f(2));
    }
}