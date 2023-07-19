<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/19 23:05
 * Email: weida_dev@163.com
 */

namespace Weida\WeixinCore;

use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use Weida\WeixinCore\Cache\FileSystemAdapter;
use Weida\WeixinCore\Cache\RedisAdapter;
use Weida\WeixinCore\Contract\AccessTokenInterface;
use Weida\WeixinCore\Contract\AccountInterface;
use Weida\WeixinCore\Contract\ApplicationInterface;
use Weida\WeixinCore\Contract\ConfigInterface;
use Weida\WeixinCore\Contract\HttpClientInterface;
use Weida\WeixinCore\Contract\RequestInterface;
use Weida\WeixinCore\Contract\StdoutLoggerInterface;

abstract class AbstractApplication implements ApplicationInterface
{
    protected ?CacheInterface $cache = null;
    protected ConfigInterface $config;
    protected ?WithAccessTokenClient $client = null;
    protected ?HttpClientInterface $httpClient = null;
    protected ?RequestInterface $request = null;
    protected ?AccountInterface $account = null;
    protected ?AccessTokenInterface $accessToken = null;
    protected ?StdoutLoggerInterface $logger=null;

    protected string $cacheNamespace="sjm";

    public function __construct(array $config)
    {
        $this->config = new Config($config);
    }

    public function getClient(): WithAccessTokenClient
    {

    }

    public function setClient(): static
    {
    }

    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    public function setConfig($config): static
    {
        if(is_array($config)){
            $this->config = new Config($config);
        }else if ($config instanceof ConfigInterface){
            $this->config = $config;
        }
        return $this;
    }

    /**
     * @return CacheInterface
     * @author Weida
     */
    public function getCache(): CacheInterface
    {
        if(empty($this->cache)){
            $cacheConfig = $this->config->get('cache');
            if(!empty($cacheConfig['redis'])){
                $this->cache = new RedisAdapter($cacheConfig['redis'],$this->getCacheNamespace());
            }else{
                $this->cache = new FileSystemAdapter();
            }
        }
        return $this->cache;
    }

    /**
     * @param CacheInterface $cache
     * @return $this
     * @author Weida
     */
    public function setCache(CacheInterface $cache):static{
        $this->cache = $cache;
        return $this;
    }

    /**
     * @return HttpClientInterface
     * @author Weida
     */
    public function getHttpClient():HttpClientInterface
    {
        if(empty($this->httpClient)){
            $this->httpClient =   new HttpClient(['base_uri'=>'https://api.weixin.qq.com/cgi-bin']);
        }
        return $this->httpClient;
    }

    /**
     * @param HttpClientInterface $httpClient
     * @return $this
     * @author Weida
     */
    public function setHttpClient(HttpClientInterface $httpClient): static
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    public function getRequest(): RequestInterface
    {
        // TODO: Implement getRequest() method.
    }

    public function setRequest(): static
    {
        // TODO: Implement setRequest() method.
    }

    public function getResponse(): ResponseInterface
    {
        // TODO: Implement getResponse() method.
    }

    public function setLogger(StdoutLoggerInterface $logger): static
    {
        $this->logger = $logger;
    }

    /**
     * @return StdoutLoggerInterface
     * @author Weida
     */
    public function getLogger(): StdoutLoggerInterface
    {
        //todo getLogger();
        if(empty($this->logger)){

        }
        return $this->logger;
    }

    /**
     * @return AccessTokenInterface
     * @author Weida
     */
    public function getAccessToken(): AccessTokenInterface
    {
        if(empty($this->accessToken)){
            $this->accessToken =  new AccessToken(
                $this->getAccount()->getAppId(),
                $this->getAccount()->getSecret(),
                $this->getCache(),
                $this->getHttpClient(),
                $this->config->get('use_stable_access_token', false),
            );
        }
        return $this->accessToken;

    }

    public function setAccessToken(AccessTokenInterface $accessToken): static
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @return string
     * @author Weida
     */
    public function getCacheNamespace(): string
    {
        return $this->cacheNamespace;
    }

    /**
     * @param string $cacheNamespace
     * @return $this
     * @author Weida
     */
    public function setCacheNamespace(string $cacheNamespace): static
    {
        $this->cacheNamespace = $cacheNamespace;
        return $this;
    }

}
