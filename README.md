# Linq in PHP

This is PHP library, that allows to query collections following C# LINQ patterns. The standard LINQ methods are implemented with
interfaces adopted to the PHP language. The full list of these methods can be taken from [MSDN, .NET 4.5](http://msdn.microsoft.com/en-us/library/vstudio/system.linq.enumerable_methods).
The modified interfaces of these methods are located in the [Qmaker\Linq\Operation](Qmaker/Linq/Operation) namespace.
At the present time the library requires PHP of the version 5.4.

## Lambda expression

The class *Lambda* allows to build lambda expressions, anonymous functions with stored structure. The class
creates a callable object, which can be used in LINQ methods as callable criteria, predicate or expression. Thus,

    $f = Lambda::v()->add()->v()->mult(12)->gt(36);

is equivalent to

    $f = function ($x) { return $x + $x*12 > 36; };

More information and examples will be added later. See [unit tests](tests/Qmaker/Linq).

## Linq

The following methods are implemented

*    Aggregation — aggregate, average, min, max, sum, count
*    Concatenation — concat, zip
*    Element — elementAt, elementAtOrDefault, first, firstOrDefault, last, lastOrDefault, single, singleOrDefault
*    Equality — isEqual
*    Filtering — ofType, where
*    Generation — from, range, repeat
*    Grouping — groupBy
*    Joining — product, join, joinOuter, groupJoin
*    Partitioning — skip, skipWhile, take, takeWhile
*    Projection — select, selectMany, cast
*    Quantifier — all, any, contains
*    Set — distinct, intersect, except, union
*    Sorting — orderBy, orderByDescending, thenBy, thenByDescending, reverse, order
*    Others — toArray, toList, each

The suited types for the source in the corresponding methods (like *from*) are **array**, **\Iterator**, **\IteratorAggregate** or **callable** variable.
As an expression one can also specify **string**, **callable** variable, **array** or lambda expression **LambdaInterface**. The following example

    $f = Linq::from([1, 2, 3, 4])->where(Lambda::v()->gt(2))->sum(Lambda::v()->mult(2));

will return 14. More information and examples will be added later. See [unit tests](tests/Qmaker/Linq).

## Iterators

The iterators are the keystone of this library. Within the project multiple iterators were additionally implemented

* **CallbackFilterIterator** Filtering items using multiple callbacks

* **CallbackIterator** Generate the sequence of the elements using a callback

* **GroupingIterator** Group elements by key

* **IndexIterator** Sort elements by key

* **JoinIterator**, **OuterJoinIterator** Inner/outer join of two iterators

* **LimitIterator** Iterator over the given range

* **ProductIterator** Cross-product of multiple iterators

* **SkipIterator** Skip items while some criteria is true

* **TakeIterator** Take items while some criteria is true

* **ProjectionIterator** Convert current values or keys

* **ReverseIterator** Reverse order

* **DistinctIterator**, **ExceptIterator**, **IntersectIterator** Sets operations

* **LazyIterator** Build inner iterator when the first item will be requested

* **VariableIterator** Re-assign the inner iterator
