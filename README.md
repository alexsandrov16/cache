# Mk4U\Cache

![GitHub Release](https://img.shields.io/github/v/release/alexsandrov16/cache?include_prereleases&style=flat-square&color=blue)
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/alexsandrov16/cache?style=flat-square)
![GitHub License](https://img.shields.io/github/license/alexsandrov16/cache?style=flat-square)

A simple and flexible cache system for PHP.

## Features
* Support for multiple cache drivers (APCu and files)
* Implementation of the `Psr\SimpleCache\CacheInterface` interface.
* Efficient storage and retrieval of data.

## Requirements
* PHP 8.2 or higher
* APCu extension (optional)

## Installation
```bash
composer require mk4u/cache
```

## Usage.

### Configuration
To use the library, you must first create an instance of the cache driver you want to use. The library includes a `Mk4U\Cache\CacheFactory` that makes it easy to create instances of the cache drivers.

> [!TIP]
> If no parameters are passed to the `Mk4U\Cache\CacheFactory::create()`, an object of type `Mk4U\Cache\Drivers\File` will be created by default.

> [!NOTE]
> By default the `Mk4U\Cache\Drivers\File` object sets the following configuration parameters:
>
> ```php
> [
>    //extension of cache files
>    'ext' =>'cache',
>    //directory where the cache will be stored, if it does not exist create it.
>    'dir' => '/cache',
>    //cache lifetime in seconds (default 5 minutes.)
>    'ttl' => 300
> ]
> ```

#### Example of use with the `Mk4U\Cache\Drivers\File` driver
```php
require 'vendor/autoload.php';

// Cache driver configuration
$config = [
    'ext' => 'txt', // Extension of cache files.
    'dir' => '/cache', // Directory where the cache will be stored
    'ttl' => 3600 // Cache lifetime in seconds.
];

// Create an instance of the file cache driver.
$cache = Mk4U\Cache\CacheFactory::create('file', $config);
```
> [!IMPORTANT]
> Make sure you set the necessary permissions for the creation of directories and cache files. 

#### Example of use with `Mk4U\Cache\Drivers\Apcu` driver
```php
require 'vendor/autoload.php';

// Cache driver configuration
$config = [
    'ttl' => 3600 // cache lifetime in seconds (default 5 minutes.)
];

// Create an instance of the APCu cache driver.
$cache = Mk4U\Cache\CacheFactory::create('apcu', $config);
```
`Mk4U\Cache\Drivers\Apcu` has only one configurable parameter and it is `ttl`, by default its value is 300 seconds (5 minutes).


### Available methods
The cache class implements the following methods of the CacheInterface interface:

* `get(string $key, mixed $default = null): mixed`
* `set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool`
* `delete(string $key): bool`
* `clear(): bool`
* `getMultiple(iterable $keys, mixed $default = null): iterable`
* `setMultiple(iterable $values, null|int|DateInterval $ttl = null): bool`
* `deleteMultiple(iterable $keys): bool`
* `has(string $key): bool`

#### Example of use of the methods

##### Storing a value in the cache
```php
// Store the value in the cache
$cache->set('my_key', 'Hello, World!');
```

##### Retrieve a value from the cache
```php
// Retrieve cache value
$cachedValue = $cache->get('my_key', 'Default value');

echo $cachedValue; // Print: Hello, World!
```

##### Remove a value from the cache
```php
// Delete the value from the cache
$cache->delete('my_key');
```

##### Checks if a value exists in the cache by its key
```php
// checks if a value exists
return $cache->has('my_key'); //false
```

##### Clear the entire cache
```php
// Clear the entire cache
$cache->clear();
```

###### Handling multiple values

You can store, retrieve and delete multiple values from the cache using the setMultiple, getMultiple and deleteMultiple methods.

###### Storing multiple values
```php
$values = [
    'key1' => 'Value 1',
    'key2' => 'Value 2'
];

$cache->setMultiple($values);
```

###### Retrieve multiple values
```php
$keys = ['key1', 'key2'];
$cachedValues = $cache->getMultiple($keys, 'Default value');

print_r($cachedValues); // Print stored values
```

###### Delete multiple values
```php
$keysToDelete = ['key1', 'key2'];
$cache->deleteMultiple($keysToDelete);
```

### Exceptions
The library throws the following exceptions:
* `Mk4U\Cache\Exceptions\CacheException`: for cache related errors.
* `Mk4U\Cache\Exceptions\InvalidArgumentException`: For invalid arguments.

## Contributions
Contributions are welcome. If you wish to contribute, please open an issue or a pull request in the repository.

## License
This project is licensed under the [MIT License](https://github.com/alexsandrov16/cache?tab=MIT-1-ov-file).

## Contact
If you have any questions or comments, feel free to contact me at [http://t.me/alexsadrov16](http://t.me/alexsadrov16)
