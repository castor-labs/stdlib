The Castor Standard Library
===========================

Main repository of the Castor Standard Library.

To install it, simply run:

```
composer require castor/stdlib
```

> NOTICE: Please note this library is in development and there is no stable version yet.

## Why this?

Traditionally, PHP has lacked of a consistent, well-designed standard library. This library is an 
attempt to provide such feature. Mainly inspired in Go's standard library, this library provides
a solid set of abstractions so your PHP projects can evolve and scale well, and nice Object Oriented
and functional APIs that are a breeze to use.

## Development Setup

Copy the `castor.bin` to one of your `$PATH` directories:

```
sudo cp ./.castor/castor.bin $HOME/.local/bin/castor
```

Bootstrap the development environment (needs docker and compose plugin):

```
castor init
```

Once bootstrapped, the `castor` tool provides easy access to many different things. This is what you can do:

- `castor php <args>`: Runs PHP inside the development container
- `castor composer <args>`: Runs composer inside the container
- `castor compose <args>`: Easy access to docker compose
- `castor shell`: Opens a shell (ash) inside the container
- `castor pr`: Checks your code meets the basic standards for a pull request