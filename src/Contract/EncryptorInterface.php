<?php
declare(strict_types=1);

/**
 * Author: Sgenmi
 * Date: 2023/7/21 9:53 AM
 * Email: 150560159@qq.com
 */

namespace Weida\WeixinCore\Contract;

interface EncryptorInterface
{
    public function decrypt(string $ciphertext, string $msgSignature, string $nonce, int|string $timestamp):string;
    public function encrypt(string $plaintext, string $nonce = '', int|string $timestamp = ''):string;

}