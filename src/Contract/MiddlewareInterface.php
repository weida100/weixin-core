<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/21 23:20
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinCore\Contract;

interface MiddlewareInterface
{
    public function handler($message,\Closure $next);
}
