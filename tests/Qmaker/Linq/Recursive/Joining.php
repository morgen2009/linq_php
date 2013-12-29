<?php

namespace Qmaker\Linq\Recursive;

use Qmaker\Fixtures\CarExample;
use Qmaker\Linq\Expression\Exp;
use Qmaker\Linq\Linq;

class JoiningTest extends \PHPUnit_Framework_TestCase {
    public function testSimpleJoin() {
        $cat = CarExample::categories();
        $car = CarExample::cars();

        $result = Linq::from($car)->alias('x')->from($cat)->alias('y')
            ->join('x', 'category.id', 'id')
            ->select([
                'car'      => 'x.title',
                'category' => 'y.title'
            ])
        ;
        $result = iterator_to_array($result);
        $this->assertEquals([
            [ 'car'=>'Opel', 'category'=>'Low'],
            [ 'car'=>'Honda', 'category'=>'Low'],
            [ 'car'=>'BMW', 'category'=>'Middle'],
            [ 'car'=>'Mercedes', 'category'=>'High'],
        ], $result);
    }

    public function testSimpleLeftJoin() {
        $cat = CarExample::categories();
        array_shift($cat);
        $car = CarExample::cars();

        $result = Linq::from($car)->alias('x')->from($cat)->alias('y')
            ->from('x')
            ->joinLeft('y', 'id', 'category.id')
            ->select([
                'car'      => 'x.title',
                'category' => 'y.title'
            ])
        ;
        $result = iterator_to_array($result);
        $this->assertEquals([
            [ 'car'=>'Opel', 'category'=>null],
            [ 'car'=>'BMW', 'category'=>'Middle'],
            [ 'car'=>'Mercedes', 'category'=>'High'],
            [ 'car'=>'Honda', 'category'=>null],
        ], $result);
    }

    public function testDefaultProduct() {
        $result = Linq::range(1, 2)->alias('x')->range(2, 3)->alias('y');
        $result = iterator_to_array($result, false);
        $this->assertEquals([
            ['x'=>1,'y'=>2],
            ['x'=>1,'y'=>3],
            ['x'=>1,'y'=>4],
            ['x'=>2,'y'=>2],
            ['x'=>2,'y'=>3],
            ['x'=>2,'y'=>4]
        ], $result);
    }

    public function testProductWithExternalLinq() {
        $result = Linq::range(1, 2)->alias('x')->product(Linq::range(2, 3)->alias('y'));
        $result = iterator_to_array($result, false);
        $this->assertEquals([
            ['x'=>1,'y'=>2],
            ['x'=>1,'y'=>3],
            ['x'=>1,'y'=>4],
            ['x'=>2,'y'=>2],
            ['x'=>2,'y'=>3],
            ['x'=>2,'y'=>4]
        ], $result);
    }

    public function testProduct() {
        $result = Linq::range(1, 2)->product(Linq::range(2, 3));
        $result = iterator_to_array($result, false);
        $this->assertEquals([
            ['a'=>1,'b'=>2],
            ['a'=>1,'b'=>3],
            ['a'=>1,'b'=>4],
            ['a'=>2,'b'=>2],
            ['a'=>2,'b'=>3],
            ['a'=>2,'b'=>4]
        ], $result);
    }

    public function testAccessingKeys() {
        $cat = CarExample::categories();
        $car = CarExample::cars();

        $result = Linq::from($car)->alias('x')->from($cat)->alias('y')
            ->join('x', 'category.id', 'id')
            ->select([
                    'car'      => Exp::index(),
                    'category' => 'y.title'
                ])
        ;
        $result = iterator_to_array($result);
        $this->assertEquals([
                [ 'car'=>'1', 'category'=>'Low'],
                [ 'car'=>'1', 'category'=>'Low'],
                [ 'car'=>'2', 'category'=>'Middle'],
                [ 'car'=>'3', 'category'=>'High'],
            ], $result);
    }
}