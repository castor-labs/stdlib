Castor UUID
===========

Simple and modern UUID library for PHP.

## Install

```bash
composer require castor/uuid
```

## Quick Start:

```php
<?php

use Castor\Uuid;

$uuid = Uuid\V4::generate();
echo $uuid->toString(); // Prints: d2e365c8-b525-428d-979f-64b70e76a217
echo $uuid->toUrn(); // Prints: urn:uuid:d2e365c8-b525-428d-979f-64b70e76a217
echo $uuid->getBytes()->toHex(); // Prints: d2e365c8b525428d979f64b70e76a217

$parsed = Uuid\parse($uuid->toString());
echo $parsed instanceof Uuid\V4; // Prints: true
echo $parsed instanceof Uuid\V3; // Prints: false
echo $parsed instanceof Uuid\V5; // Prints: false
echo $parsed->equals($uuid); // Prints: true
```

The same API is available for `Uuid\V3` and `Uuid\V5`. 

`Uuid\V1` has not yet been implemented.

`Uuid\V2` will not be implemented.