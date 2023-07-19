<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/19 22:57
 * Email: weida_dev@163.com
 */

namespace Weida\WeixinCore\Contract;

interface ConfigInterface
{
    /**
     * @param string $key
     * @param mixed $val
     * @return void
     * @author Weida
     */
    public function set(string $key,mixed $val):void;

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     * @author Weida
     */
    public function get(string $key, mixed $default=''):mixed;

    /**
     * @param string $key
     * @return bool
     * @author Weida
     */
    public function has(string $key):bool;

}
