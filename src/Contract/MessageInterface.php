<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/8/23 22:43
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinCore\Contract;

interface MessageInterface
{
    public function setAttributes(array|string $attributes):void;
    public function getAttributes():array;
}
