<?php
declare(strict_types=1);
/**
 * Author: weida
 * Date: 2023/7/19 22:55
 * Email: weida_dev@163.com
 */

namespace Weida\WeixinCore\Contract;

interface AesInterface
{
    public function encrypt(string $data, string $passphrase, int $options = 0, string $iv = ""):string;
    public function decrypt(string $data, string $passphrase, int $options = 0, string $iv = ""):string;

}
