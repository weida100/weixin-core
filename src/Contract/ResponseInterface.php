<?php
declare(strict_types=1);
/**
 * Author: sgenmi
 * Date: 2023/7/20 23:35
 * Email: 150560159@qq.com
 */

namespace Weida\WeixinCore\Contract;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
interface ResponseInterface
{
    public function response():PsrResponseInterface;
    public function with(callable|string|array|object $callbale):static;
    public function addMessageListener(string $msgType,callable|string|array|object $handler):static;
    public function addEventListener(string $msgType,callable|string|array|object $handler):static;
    public function getRequestMessage():string;
    public function getDecryptedMessage(): array|string;
}
