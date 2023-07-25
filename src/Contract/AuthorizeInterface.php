<?php
declare(strict_types=1);
/**
 * Author: sgenmi
 * Date: 2023/7/25 23:28
 * Email: 150560159@qq.com
 */

namespace Weida\WeixinCore\Contract;

interface AuthorizeInterface
{
    public function createPreAuthorizationUrl(string $redirect_uri):string;
    public function refreshAuthorizerToken(string $authorizerAppId, string $authorizerRefreshToken):array;
    public function getAuthorization(string $authorizationCode):array;
}
