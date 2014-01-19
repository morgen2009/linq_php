* [soon] ComplexKeyInterface with method keys(), which returns complex key (array, object, etc) for iterator

* cache Linq::getIterator() result

* implement Lambda::math($pattern, $params, ...)
  Example: Lambda::math('1+{1}/{0}', Lambda::x, Lambda::y) === function ($x, $y) { return 1 + $y/$x; }

* add "dev-master" branch

* use OrderedSet instead of HashSet in DistinctIterator

* implement IEnumerable a facade for Doctrine

* implement Generation::defaultIfEmpty and empty methods