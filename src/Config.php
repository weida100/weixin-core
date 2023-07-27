<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/19 22:58
 * Email: weida_dev@163.com
 */

namespace Weida\WeixinCore;

use Weida\WeixinCore\Contract\ConfigInterface;

class Config implements ConfigInterface
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function set(string $key, mixed $val): void
    {
        $this->config[$key] = $val;
    }

    public function get(string $key, mixed $default = ''): mixed
    {
        if(!$key){
            return $this->config;
        }
        $keys = explode('.',$key);
        $val= null;
        foreach ($keys as $v){
            if(is_null($val)){
                $val = $this->config[$v]??'';
            }else{
                if(!$val){
                    return $default;
                }else{
                    $val = $val[$v]??'';
                }
            }
        }
        return $val;
    }

    public function has(string $key): bool
    {
        return isset($this->config[$key]);
    }

}
