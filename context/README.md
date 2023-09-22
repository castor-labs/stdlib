Castor Serde
============

Immutable context abstraction for your PHP project.

## Install

```bash
composer require castor/context
```

## Quick Start

```php
<?php

use Castor\Context;

$ctx = Context\nil();
$ctx = Context\withValue($ctx, 'key', 'value');

echo $ctx->value('key'); // Prints: value
```