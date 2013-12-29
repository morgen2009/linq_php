<?php

namespace Qmaker\Fixtures;

class CarExample {
    static function categories() {
        $cat = array();
        $cat[] = Category::instance([ 'id' => 1, 'title' => 'Low', 'rank' => 1 ]);
        $cat[] = Category::instance([ 'id' => 2, 'title' => 'Middle', 'rank' => 2 ]);
        $cat[] = Category::instance([ 'id' => 3, 'title' => 'High', 'rank' => 3 ]);

        return $cat;
    }

    static function cars() {
        $cat = array();
        $cat[] = Category::instance([ 'id' => 1, 'title' => 'Low', 'rank' => 1 ]);
        $cat[] = Category::instance([ 'id' => 2, 'title' => 'Middle', 'rank' => 2 ]);
        $cat[] = Category::instance([ 'id' => 3, 'title' => 'High', 'rank' => 3 ]);

        $cars = array();
        $cars[] = Car::instance([ 'id' => 1, 'title' => 'Opel', 'price' => 16000, 'category' => $cat[0] ]);
        $cars[] = Car::instance([ 'id' => 2, 'title' => 'BMW', 'price' => 20000, 'category' => $cat[1] ]);
        $cars[] = Car::instance([ 'id' => 3, 'title' => 'Mercedes', 'price' => 30000, 'category' => $cat[2] ]);
        $cars[] = Car::instance([ 'id' => 4, 'title' => 'Honda', 'price' => 16000, 'category' => $cat[0] ]);

        return $cars;
    }
}
