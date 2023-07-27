<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/19 23:03
 * Email: weida_dev@163.com
 */

namespace Weida\WeixinCore;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use RuntimeException;
use Weida\WeixinCore\Contract\AccessTokenInterface;
use Weida\WeixinCore\Contract\HttpClientInterface;
use Throwable;

class AccessToken implements AccessTokenInterface
{
    private string $appId='';
    private string $secret='';
    private ?CacheInterface $cache=null;
    private ?HttpClientInterface $httpClient=null;
    private bool $isStable=false;
    private string $cacheKey='';

    public function __construct(
        string $appId, string $secret, ?CacheInterface $cache=null, ?HttpClientInterface $httpClient=null, bool $isStable=false
    )
    {
        $this->appId = $appId;
        $this->secret = $secret;
        $this->cache = $cache;
        $this->httpClient = $httpClient;
        $this->isStable = $isStable;
    }

    public function setCacheKey(string $key):static{
        $this->cacheKey = $key;
        return $this;
    }

    /**
     * @return string
     * @author Weida
     */
    public function getCacheKey(): string
    {
        if(empty($this->cacheKey)){
            $this->cacheKey = sprintf("access_token:%s",$this->appId);
        }
        return $this->cacheKey;
    }

    /**
     * @param bool $isRefresh
     * @return string
     * @throws Throwable|InvalidArgumentException
     * @author Weida
     */
    public function getToken(bool $isRefresh=false): string
    {
        if(!$isRefresh){
            $token = $this->cache->get($this->getCacheKey());
            if (!empty($token)) {
                return $token;
            }
        }
        return $this->refresh();
    }

    /**
     * @return string
     * @throws Throwable|InvalidArgumentException
     * @author Weida
     */
    private function refresh():string{
        if($this->isStable){
            $apiUrl = '/cgi-bin/stable_token';
            $params=[
                'json' => [
                    'grant_type' => 'client_credential',
                    'appid' => $this->appId,
                    'secret' => $this->secret,
                    'force_refresh' => false
                ],
            ];
            $method = "POST";
        }else{
            $apiUrl = '/cgi-bin/token';
            $params = [
                'query' => [
                    'grant_type' => 'client_credential',
                    'appid' => $this->appId,
                    'secret' => $this->secret,
                ],
            ];
            $method = "GET";
        }
        $resp = $this->httpClient->request($method, $apiUrl,$params);
        if($resp->getStatusCode()!=200){
            throw new RuntimeException('Request access_token exception');
        }
        $arr = json_decode($resp->getBody()->getContents(),true);

        if (empty($arr['access_token'])) {
            throw new RuntimeException('Failed to get access_token: ' . json_encode($arr, JSON_UNESCAPED_UNICODE));
        }
        $this->cache->set($this->getCacheKey(), $arr['access_token'], intval($arr['expires_in']));
        return $arr['access_token'];
    }

    /**
     * @return int
     * @author Weida
     */
    public function expiresTime(): int
    {
        return  $this->cache->ttl($this->getCacheKey());
    }

    /**
     * @return array
     * @author Weida
     */
    public function getParams(): array
    {
        return [
            'appid'=>$this->appId,
            'secret'=>$this->secret,
            'cache'=>$this->cache,
            'httpClient'=>$this->httpClient,
            'stable'=>$this->isStable,
        ];
    }


}
