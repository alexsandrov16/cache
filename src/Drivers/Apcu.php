<?php

namespace Mk4U\Cache\Drivers;

use Mk4U\Cache\Exceptions\CacheException;
use Psr\SimpleCache\CacheInterface;

/**
 * APCU class
 */
class Apcu implements CacheInterface
{
    protected int $ttl = 300;
    //protected ?string $prefix = null;

    public function __construct(array $config)
    {
        if (!extension_loaded('apcu')) {
            throw new CacheException('Error: APCu is not enabled.');
        }

        $this->ttl = $config['ttl'] ?? $this->ttl;
        //$this->prefix = $config['prefix'] ?? '';
    }

    /**
     * Recupera un valor de la caché por su clave.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if ($this->has($key)) {
            return apcu_fetch($key);
        }
        return $default;
    }

    /**
     * Almacena un valor en la caché con una clave especificada.
     */
    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        if ($this->has($key)) {
            $this->delete($key);
        }

        return apcu_store($key, $value, $ttl ?? $this->ttl);
    }

    /**
     * Elimina un valor de la caché por su clave.
     */
    public function delete(string $key): bool
    {
        if ($this->has($key)) {
            return apcu_delete($key);
        }

        return false;
    }

    /**
     * Limpia toda la caché.
     */
    public function clear(): bool
    {
        return apcu_clear_cache();
    }

    /**
     * Recupera múltiples valores de la caché por sus claves.
     *
     * @param iterable $keys Una colección iterable de claves de caché.
     * @param mixed $default El valor por defecto a devolver para las claves que no existen.
     * @return iterable Un array asociativo de claves y sus correspondientes 
     * valores almacenados en caché.
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $values = [];

        foreach ($keys as $key) {
            $values[$key] = $this->get($key, $default);
        }
        return $values;
    }

    /**
     * Almacena múltiples valores en la caché.
     *
     * @param iterable $values Una colección iterable de pares clave-valor para 
     * almacenar en la caché.
     * @param null|int|\DateInterval $ttl Tiempo de vida opcional para los elementos 
     * de caché.
     * @return bool Verdadero en caso de éxito, falso en caso de fallo.
     */
    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        $result = [];

        foreach ($values as $key => $value) {
            $result[] = $this->set($key, $value, $ttl);
        }

        return in_array(false, $result) ? false : true;
    }

    /**
     * Elimina múltiples valores de la caché por sus claves.
     *
     * @param iterable $keys Una colección iterable de claves de caché a eliminar.
     * @return bool Verdadero en caso de éxito, falso en caso de fallo.
     */
    public function deleteMultiple(iterable $keys): bool
    {
        $result = [];

        foreach ($keys as $key) {
            $result[] = $this->delete($key);
        }

        return in_array(false, $result) ? false : true;
    }

    /**
     * Verifica si un valor existe en la caché por su clave.
     */
    public function has(string $key): bool
    {
        return apcu_exists($key);
    }
}
