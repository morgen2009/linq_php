<?php
namespace Qmaker\Lambda;

use Qmaker\Linq\Expression as ExpOld;
use Qmaker\Lambda as ExpNew;

class ExpressionBenchmarkTest extends \PHPUnit_Framework_TestCase {
    public function testMemory() {
        $m1 = memory_get_usage();
        $f1 = ExpOld\Lambda::v()->add(1); // x -> x+1
        $m1_1 = memory_get_usage();
        $f1 = ExpOld\Lambda::v()->add(1);
        $m2 = memory_get_usage();
        $f2 = ExpNew\Lambda::define()->x()->add(1);
        $m2_1 = memory_get_usage();
        $f2 = ExpNew\Lambda::define()->x()->add(1);
        $m3 = memory_get_usage();

        $memory = [
            $m1_1-$m1-$m2+$m1_1, // OLD: php code
            $m2-$m1_1, // OLD: expression
            $m2_1-$m2-$m3+$m2_1, // NEW: php code
            $m3-$m2_1, // NEW: expression
        ];

        print_r($memory);
    }

    public function testSpeed() {
        $f1 = ExpOld\Lambda::v()->add(1); // x -> x+1
        $f2 = ExpNew\Lambda::define()->x()->add(1);
        $f3 = function ($x) { return $x+1; };

        $cnt = 10000;
        $m1 = microtime(true);
        for ($i=$cnt; $i>=0; $i--) {
            $f1($i);
        }
        $m1 = microtime(true) - $m1;

        $m2 = microtime(true);
        for ($i=$cnt; $i>=0; $i--) {
            $f2($i);
        }
        $m2 = microtime(true) - $m2;

        $m3 = microtime(true);
        for ($i=$cnt; $i>=0; $i--) {
            $f3($i);
        }
        $m3 = microtime(true) - $m3;

        $time = [
            $m1, // old expression
            $m2, // new expression
            $m3, // native anonymous function
        ];
        print_r($time);
    }
}
 