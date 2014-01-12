<?php

namespace Qmaker\Linq\Expression;


class ExpressionBuilderTest extends \PHPUnit_Framework_TestCase {
    public function testSimple()
    {
        $build = new ExpressionBuilder();
        $build->add(1)->add('+', 1)->add(2);
        $this->assertEquals([1, 2, 2, '+'], $build->export());
    }

    public function testTwoPriorities()
    {
        $build = new ExpressionBuilder();
        $build->add(1)->add('+', 1)->add(2)->add('*', 2)->add(3);
        $this->assertEquals([1, 2, 3, 2, '*', 2, '+'], $build->export());
    }

    public function testMultipleOperationsWithSamePriority()
    {
        $build = new ExpressionBuilder();
        $build->add(1)->add('+', 1)->add(2)->add('+', 1)->add(3);
        $this->assertEquals([1, 2, 3, 3, '+'], $build->export());
    }
}