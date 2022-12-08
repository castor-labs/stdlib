The Castor Standard Library
===========================

Main repository of the Castor Standard Library.

To install it, simply run:

```
composer require castor/stdlib
```

Each package is also individually published in their own repo from a subtree split of this one, 
should you need to import only a subset of this standard library.

Simply require `castor/<package-name>` to add it.

## Why this?

Traditionally, PHP has lacked of a consistent, well-designed standard library. This library is an 
attempt to provide such feature. Mainly inspired in Go's standard library, this library provides
a solid set of abstractions so your PHP projects can evolve and scale well, and nice Object Oriented
and functional APIs that are a breeze to use.