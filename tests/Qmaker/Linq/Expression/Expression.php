<?php

namespace Qmaker\Linq\Expression;

use Qmaker\Linq\Expression\Operation\Comparison;
use Qmaker\Linq\Expression\Operation\Math;

class ExpressionTest extends \PHPUnit_Framework_TestCase {
    public function testMath_Sum() {
        $builder = new ExpressionBuilder();
        $builder->add(1)->add(new Math(Math::ADD), 1)->add(2)->add(new Math(Math::ADD), 1)->add(3);
        $func = new Expression($builder->export());
        $this->assertEquals(6, $func->__invoke());
    }

    public function testMath_SumAndMult() {
        $builder = new ExpressionBuilder();
        $builder->add(1)->add(new Math(Math::ADD), 1)->add(2)->add(new Math(Math::MULT), 2)->add(3);
        $func = new Expression($builder->export());
        $this->assertEquals(7, $func->__invoke());
    }

    public function testComparison_GT() {
        $builder = new ExpressionBuilder();
        $builder->add(1)->add(new Comparison(Comparison::_LT_), 1)->add(2);
        $func = new Expression($builder->export());
        $this->assertEquals(true, $func->__invoke());
    }

    public function testCallable() {
        $builder = new ExpressionBuilder();
        $builder->add(1)->add(new Comparison(Comparison::_LT_), 1)->add(function ($value) { return $value; })->add(new Comparison(Comparison::_LT_), 1)->add(2);
        $func = new Expression($builder->export());
        $this->assertEquals(true, $func->__invoke(1.5), '1.5');
        $this->assertEquals(false, $func->__invoke(2), '2.0');
    }
}
 