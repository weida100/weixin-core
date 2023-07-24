<?php
declare(strict_types=1);
/**
 * Author: weida
 * Date: 2023/7/24 21:16
 * Email: weida_dev@163.com
 */

namespace Weida\WeixinCore\Contract;

interface VerifyTicketInterface
{
   public function getTicket():string;
   public function setTicket(string $ticket,int $ttl=43000):static;
   public function setCacheKey(string $key):static;
   public function getCacheKey():string;

}
