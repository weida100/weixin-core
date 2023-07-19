<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/19 23:06
 * Email: weida_dev@163.com
 */

namespace Weida\WeixinCore\Cache;

use InvalidArgumentException;
use Psr\SimpleCache\CacheInterface;

class RedisAdapter implements CacheInterface
{
    private $redis;
    private string $prefix='';
    public function __construct($redis,$prefix=""){
        $this->redis = $redis;
        $this->prefix = $prefix;
    }

    public function get(string $key, $default = null): mixed
    {
        $redis = $this->getRedis();
        $val = $redis->get($this->getCompleteKey(strval($key)));
        return $val ?unserialize($val):$default;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param null $ttl
     * @return bool
     */
    public function set(string $key, mixed $value, $ttl = null): bool
    {
        $redis = $this->getRedis();
        $key = $this->getCompleteKey(strval($key));
        $redis->sadd($this->getCompleteKey('s:all:wechat:cache'), $key);
        $value = serialize($value);
        return $redis->set($key,$value,$ttl);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        $redis = $this->getRedis();
        return boolval($redis->del($this->getCompleteKey(strval($key))));
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
        $redis = $this->getRedis();
        $keys = $redis->sMembers($this->getCompleteKey('s:all:wechat:cache'));
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }


    /**
     * @param array $keys
     * @param mixed|null $default
     * @return array
     * @author Weida
     */
    public function getMultiple(iterable $keys, mixed $default = null): array
    {
        if ($keys instanceof \Traversable) {
            $keys = iterator_to_array($keys, false);
        } elseif (!\is_array($keys)) {
            throw new InvalidArgumentException(sprintf('Cache keys must be array or Traversable, "%s" given.', get_debug_type($keys)));
        }
        $keys = array_map(function ($v){
            return $this->getCompleteKey(strval($v));
        },$keys);
        $redis = $this->getRedis();
        $val = $redis->mget($keys);
        $ret=[];
        foreach ($keys as $k=>$v){
            $_v = $default;
            if(isset($val[$k])){
                $_v = unserialize($val[$k]);
            }
            $ret[$v]=$_v;
        }
        return $ret;
    }


    /**
     * @param iterable $values
     * @param null $ttl
     * @return bool
     * @author Weida
     */
    public function setMultiple(iterable $values, $ttl = null): bool
    {
        if (!is_array($values) && !$values instanceof \Traversable) {
            throw new InvalidArgumentException(sprintf('Cache values must be array or Traversable, "%s" given.', get_debug_type($values)));
        }
        foreach ($values as $key => $value) {
            $this->set($key, $values, $ttl);
        }
        return true;
    }

    /**
     * @param iterable $keys
     * @return bool
     */
    public function deleteMultiple(iterable $keys): bool
    {
        if ($keys instanceof \Traversable) {
            $keys = iterator_to_array($keys, false);
        } elseif (!is_array($keys)) {
            throw new InvalidArgumentException(sprintf('Cache keys must be array or Traversable, "%s" given.', get_debug_type($keys)));
        }
        foreach ($keys as $key => $value) {
            $this->delete($key);
        }
        return true;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        $redis = $this->getRedis();
        $key = $this->getCompleteKey($key);
        return boolval($redis->exists($key));
    }

    /**
     * @param $key
     * @return int
     * @author Weida
     */
    public function ttl($key):int{
        $redis = $this->getRedis();
        $key = $this->getCompleteKey($key);
        return $redis->ttl($key);
    }

    private function getRedis(){
        return $this->redis;
    }

    /**
     * @param string $key
     * @return string
     * @author Weida
     */
    private function getCompleteKey(string $key):string {
        if(!empty($this->prefix)){
            return $this->prefix.":".$key;
        }
        return $key;
    }

}
