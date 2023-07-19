<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/19 23:07
 * Email: weida_dev@163.com
 */

namespace Weida\WeixinCore\Cache;

use Psr\SimpleCache\CacheInterface;

class FileSystemAdapter implements CacheInterface
{
    public function __construct()
    {
    }

    public function get($key, $default = null): mixed
    {
        // TODO: Implement get() method.
    }

    public function set($key, $value, $ttl = null): bool
    {
        // TODO: Implement set() method.
    }

    public function delete($key): bool
    {
        // TODO: Implement delete() method.
    }

    public function clear(): bool
    {
        // TODO: Implement clear() method.
    }

    public function getMultiple($keys, $default = null): iterable
    {
        // TODO: Implement getMultiple() method.
    }

    public function setMultiple($values, $ttl = null): bool
    {
        // TODO: Implement setMultiple() method.
    }

    public function deleteMultiple($keys): bool
    {
        // TODO: Implement deleteMultiple() method.
    }

    public function has($key): bool
    {
        // TODO: Implement has() method.
    }

    public function ttl($key):int {
        return 0;
    }


}
