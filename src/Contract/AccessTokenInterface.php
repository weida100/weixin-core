<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/19 23:00
 * Email: weida_dev@163.com
 */

namespace Weida\WeixinCore\Contract;

interface AccessTokenInterface
{
    public function getToken(bool $isRefresh=false):string;
    public function expiresTime():int;
    public function getParams():array;
    public function getCacheKey():string;
    public function setCacheKey(string $key):static;

}
