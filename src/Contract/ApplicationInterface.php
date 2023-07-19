<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/19 23:02
 * Email: weida_dev@163.com
 */

namespace Weida\WeixinCore\Contract;

use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use Weida\WeixinCore\Encoder;
use Weida\WeixinCore\WithAccessTokenClient;

interface ApplicationInterface
{
    public function getAccount(): AccountInterface;
    public function getEncryptor(): Encoder;
    public function getResponse(): ResponseInterface;

    public function getRequest(): RequestInterface;
    public function setRequest(): static;

    public function getClient(): WithAccessTokenClient;
    public function setClient(): static;

    public function getHttpClient(): HttpClientInterface;
    public function setHttpClient(HttpClientInterface $httpClient): static;

    public function getConfig(): ConfigInterface;
    public function setConfig($config): static;

    public function getCache(): CacheInterface;
    public function setCache(CacheInterface $cache): static;

    public function getAccessToken():AccessTokenInterface;
    public function setAccessToken(AccessTokenInterface $accessToken):static;


    public function setLogger(StdoutLoggerInterface $logger):static;
    public function getLogger():StdoutLoggerInterface;

    public function getCacheNamespace():string;
    public function setCacheNamespace(string $cacheNamespace):static;
}
