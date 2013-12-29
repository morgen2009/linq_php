# Linq in PHP

This is PHP library, that allows to query and update the collections following C# LINQ patterns. In the project, all
standard enumerable methods from .NET framework 4.5 are implemented with similar interfaces adopted to the PHP language.
The full list of these methods can be found in [MSDN](http://msdn.microsoft.com/en-us/library/vstudio/system.linq.enumerable_methods)
and those modified interfaces are in the [Qmaker\Linq\Operation](lib/Qmaker/Linq/Operation) namespace. The enumerable methods are
similar to the corresponding SQL commands (SELECT, WHERE, JOIN, etc.) and can be directly transformed into SQL.
The current implementation is completely based on iterators and requires the PHP v5.3.

Developing this library I was impressed by the features of C# and would like to bring this one into the PHP world. I would appreciate any
remarks from the community regarding the project, the performance or the code style. In presence of any positive feedback I would also
write the documentation and fix my english.

# Main features / examples

Different use cases are presented in the unit tests within the library. Here I list the main part of the features with corresponding examples.

    $numbers = [1, 2, 3, 4];
    $cars = CarFactory::instances();
    $categories = CategoryFactory::instances();

[x] array, iterator, callable as input

    // [1,2,3]
    Linq::from(new \ArrayIterator([1,2,3]));
    Linq::from([1,2,3]);
    Linq::range(1,3);

    // ['a','a','a']
    Linq::repeat('a',3);

[x] different types of expressions (strings, callable, array)

    Linq::from($cars)->select('price');
    Linq::from($cars)->select(function (Car $c) { return $c->getPrice(); });
    Linq::from($cars)->select([
        'car'      => 'name',
        'category' => 'category.name',
        'price'    => 'price'
    ]);

[x] filtering

    Linq::from($numbers)->where(function ($n) { return $n > 1; })->skip(1);

[x] aggregation

    Linq::from($cars)->sum('price');
    Linq::from($numbers)->min();

[x] sorting

    Linq::from($cars)->orderBy('price');
    Linq::from($cars)->orderBy('year')->thenBy('price');

[x] joining and cross-product

    // inner&outer join
    Linq::from($cars)->join(Linq::from(category), 'id', 'category.id');

    // cross-product
    Linq::from($a)->from($b);
    Linq::from($a)->product(Linq::from($b)->take(3));

[x] grouping and late execution

    Linq::from($cars)->groupBy('category.id')->select([
        'category' => Exp::group(),
        'avr_price' => Linq::exp()->average('price')
    ]);

[x] quantifier

    Linq::from($category)->any(function (Category $c) {
        return $c->getCars() == null;
    });

[x] element accessing

    Linq::from($category)->first();
    Linq::from($category)->last();

[x] sets operations

    Linq::from($category)->except($vip_category);
    Linq::from($category)->selectMany('cars')->distinct('id');

# Iterators

All enumerable methods are based on the iterators. Many iterators are missed in the standard PHP build. They are additionally
implemented within the project in the [Qmaker\Linq\Iterators](lib/Qmaker/Linq/Iterators) namespace.

* **CallbackIterator** Generate the sequence of the elements using a callable

* **GroupingIterator** Group elements by the key function

* **IndexIterator** Sort elements by the key function

* **JoinIterator** Inner/outer join pf two iterators

* **ProductIterator** Cross-product of multiple iterators

* **MultiCallbackFilterIterator** Filtering items using multiple callbacks

* **SkipIterator** Skip items while the expression is true

* **TakeIterator** Take items while the expression is true

* **ProjectionIterator** Convert the item to the new one

* **ReverseIterator** Reverse the order

* **DistinctIterator**, **ExceptIterator**, **IntersectIterator** Sets operations

* **LazyIterator** Open inner iterator only of those first item will be requested

* **OuterIterator** Re-assign the inner iterator

