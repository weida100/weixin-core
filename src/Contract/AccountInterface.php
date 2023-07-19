<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/19 22:59
 * Email: weida_dev@163.com
 */

namespace Weida\WeixinCore\Contract;

interface AccountInterface
{
    public function getAppId(): string;

    public function getSecret(): string;

    public function getToken(): string;

    public function getAesKey(): string;

}
