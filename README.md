# Mk4U\Cache

A simple and flexible cache system for PHP.

## Features

* Support for multiple cache drivers (APCu and files)
* Implementation of the `Psr\SimpleCache\CacheInterface` interface.
* Efficient storage and retrieval of data.

## Installation

```bash
composer require mk4u/cache
```

## Requirements

* PHP 8.2 or higher
* APCu extension (optional)

## Usage.

```php
use Mk4U\Cache\CacheFactory;

$cache = CacheFactory::create('apcu');
$cache->set('key', 'value');
echo $cache->get('key'); // print “value”.
```

## License

MIT