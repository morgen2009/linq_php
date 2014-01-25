<?php

namespace Qmaker\Lambda;


use Qmaker\Lambda\Operators\Math;

class ExpressionTest extends \PHPUnit_Framework_TestCase {
    protected function printExport(Expression $e) {
        $e->compile();
        $result = $e->getCompiledData();
        return $result;
    }

    public function testAddition() {
        $e = new Expression();
        $e->addData(1);
        $e->addOperator(Math::instance(Math::ADD));
        $e->addData(2);

        $this->assertEquals([1, 2, 2, Math::instance(Math::ADD)], $this->printExport($e), 'Build');
        $this->assertEquals(3, $e(), 'Compute');
    }

    public function testMultiplication() {
        $e = new Expression();
        $e->addData(1)
          ->addOperator(Math::instance(Math::ADD))
          ->addData(2)
          ->addOperator(Math::instance(Math::MULT))
          ->addData(3);
        $this->assertEquals([1, 2, 3, 2, Math::instance(Math::MULT), 2, Math::instance(Math::ADD)], $this->printExport($e), 'Build');
        $this->assertEquals(7, $e(), 'Compute');
    }

    public function testBracket()
    {
        $e = new Expression();
        $e->addData(2);
        $e->addOperator(Math::instance(Math::MULT));
        $e->with()
            ->addData(2)
            ->addOperator(Math::instance(Math::ADD))
            ->addData(3)
            ->end();
        $this->assertEquals([2, 2, 3, 2, Math::instance(Math::ADD), 2, Math::instance(Math::MULT)], $this->printExport($e), 'Build');
        $this->assertEquals(10, $e(), 'Compute');
    }

    public function testRootBracket()
    {
        $e = new Expression();
        $e->with()
            ->addData(1)
            ->addOperator(Math::instance(Math::ADD))
            ->addData(2)
            ->end();
        $this->assertEquals([1, 2, 2, Math::instance(Math::ADD)], $this->printExport($e), 'Build');
        $this->assertEquals(3, $e(), 'Compute');
    }

    public function testEmptyBracket()
    {
        $e = new Expression();
        $e->addData(1)
            ->addOperator(Math::instance(Math::ADD))
            ->with()
            ->end();
        $this->assertEquals([1, 1, Math::instance(Math::ADD)], $this->printExport($e), 'Build');
        $this->assertEquals(1, $e(), 'Compute');
    }
}
 