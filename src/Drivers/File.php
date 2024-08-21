<?php

namespace Mk4U\Cache\Drivers;

use Mk4U\Cache\Exceptions\CacheException;
use Psr\SimpleCache\CacheInterface;

/**
 * undocumented class
 */
class File implements CacheInterface
{
    /** @param string $ext Extencion de los archivos de cache*/
    protected string $ext = 'cache';

    /** @param string|null $namespace Espacio de nombre de los subdirecorios 
     * donde se almacenaran los archivos de cache
     */
    protected ?string $namespace = null;

    /** @param string|null $cacheDir Directorio raiz donde se almacenara toda la cache*/
    protected ?string $cacheDir = null;

    /** @param int|null $ttl Tiempo de vida de la cache*/
    protected ?int $ttl = null;

    public function __construct(array $config)
    {
        //Establecer los parametros
        $this->ext = $config['ext'] ?? $this->ext;
        $this->namespace = $config['namespace'] ?? '';
        $this->cacheDir = !empty($config['dir']) ? trim($config['dir'], '/') : dirname(__DIR__, 4) . '/cache';
        $this->ttl = $config['ttl'] ?? 300;

        if (!is_dir($this->cacheDir) && !mkdir($this->cacheDir, 0775, true)) {
            throw new CacheException("No permissions to create the directory {$this->cacheDir}");
        }
    }

    private function getCache(string $key): mixed
    {
        if ($this->has($key)) {
            return unserialize(file_get_contents($this->filePath($key)));
        }
        return null;
    }

    /**
     * Recupera un valor de la caché por su clave.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (is_null($cache = $this->getCache($key))) {
            return $default;
        }
        return $cache['data'];
    }

    /**
     * Almacena un valor en la caché con una clave especificada.
     */
    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        if ($this->has($key)) {
            $this->delete($key);
        }

        $ttl = is_null($ttl) ? $this->ttl : $ttl;

        // Si el TTL es un objeto DateInterval, convertirlo a segundos
        if ($ttl instanceof \DateInterval) {
            $ttl = $ttl->days * 86400 + $ttl->h * 3600 + $ttl->i * 60 + $ttl->s;
        }


        $data = [
            'data' => $value,
            'expire' => time() + $ttl
        ];

        return file_put_contents(
            $this->filePath($key),
            serialize($data),
            LOCK_EX
        ) !== false;
    }

    /**
     * Elimina un valor de la caché por su clave.
     */
    public function delete(string $key): bool
    {
        if ($this->has($key)) {
            return unlink($this->filePath($key));
        }

        return false;
    }

    /**
     * Limpia toda la caché.
     */
    public function clear(): bool
    {
        return rmdir($this->cacheDir);
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
        return file_exists($this->filePath($key));
    }

    /**
     * Establece el espaco de nombre o subdirectorio del archivo
     */
    public function setNamespace(string $namespace): CacheInterface
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * Verifica si expiro la cache
     */
    public function isExpired(array|string $cache): bool
    {
        $expire = null;
        if (is_array($cache)) {
            $expire = $cache;
        }

        if (is_string($cache)) {
            $expire = $this->getCache($cache);
        }

        return isset($expire) ? $expire['expire'] <= time() : true;
    }

    /**
     * Establece el nombre del archivo de cacheo
     */
    private function filePath(string $name): string
    {
        $subdir = trim($this->namespace, '/');
        return "$this->cacheDir/$subdir/$name.$this->ext";
    }
}
