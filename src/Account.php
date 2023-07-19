<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/19 23:04
 * Email: weida_dev@163.com
 */

namespace Weida\WeixinCore;

use InvalidArgumentException;
use Weida\WeixinCore\Contract\AccountInterface;

class Account implements AccountInterface
{
    protected string $appId;
    protected string $secret="";
    protected string $token='';
    protected string $aesKey='';

    public function __construct( string $appId, string $secret='', string $token = '', string $aesKey = '') {
        $this->appId = $appId;
        $this->secret = $secret;
        $this->token = $token;
        $this->aesKey = $aesKey;
    }

    public function getAppId(): string
    {
        if (empty($this->appId)) {
            throw new InvalidArgumentException('Invalid app_id parameter');
        }
        return $this->appId;
    }

    public function getSecret(): string
    {
        if (empty($this->secret)) {
            throw new InvalidArgumentException('Invalid secret parameter');
        }
        return $this->secret;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getAesKey(): string
    {
        return $this->aesKey;
    }
}
