<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/19 23:05
 * Email: weida_dev@163.com
 */

namespace Weida\WeixinCore;

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use Psr\SimpleCache\CacheInterface;
use Weida\WeixinCore\Cache\FileSystemAdapter;
use Weida\WeixinCore\Cache\RedisAdapter;
use Weida\WeixinCore\Contract\AccessTokenInterface;
use Weida\WeixinCore\Contract\AccountInterface;
use Weida\WeixinCore\Contract\ApplicationInterface;
use Weida\WeixinCore\Contract\ConfigInterface;
use Weida\WeixinCore\Contract\EncryptorInterface;
use Weida\WeixinCore\Contract\HttpClientInterface;
use Weida\WeixinCore\Contract\RequestInterface;
use Weida\WeixinCore\Contract\StdoutLoggerInterface;
use Weida\WeixinCore\Contract\WithAccessTokenClientInterface;
use Weida\WeixinCore\Contract\ResponseInterface;

abstract class AbstractApplication implements ApplicationInterface
{
    protected ?CacheInterface $cache = null;
    protected ConfigInterface $config;
    protected ?WithAccessTokenClientInterface $client = null;
    protected ?HttpClientInterface $httpClient = null;
    protected RequestInterface|ServerRequestInterface|null $request = null;
    protected ?AccountInterface $account = null;
    protected ?AccessTokenInterface $accessToken = null;
    protected ?StdoutLoggerInterface $logger=null;
    protected ?EncryptorInterface $encryptor = null;
    protected ?ResponseInterface $response=null;

    protected string $cacheNamespace="sjm";

    public function __construct(array $config)
    {
        $this->config = new Config($config);
    }

    public function getClient(): WithAccessTokenClientInterface
    {
        if(empty($this->client)){
            $this->client = new WithAccessTokenClient(
                $this->getHttpClient(),
                $this->getAccessToken()
            );
        }
        return $this->client ;
    }

    public function setClient(WithAccessTokenClientInterface $client): static
    {
        $this->client = $client;
        return $this;
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

    public function getRequest(): RequestInterface|ServerRequestInterface
    {
        if(empty($this->request)){
            $this->request = ServerRequest::fromGlobals();
        }
        return $this->request;
    }

    public function setRequest(RequestInterface|ServerRequestInterface $request): static
    {
        $this->request = $request;
        return $this;
    }

    public function setLogger(StdoutLoggerInterface $logger): static
    {
        $this->logger = $logger;
        return $this;
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

    /**
     * @return EncryptorInterface
     * @author Weida
     */
    public function getEncryptor(): EncryptorInterface
    {
        if(empty($this->encryptor)){
            $this->encryptor = new Encryptor(
                $this->getAccount()->getAppId(),
                $this->getAccount()->getToken(),
                $this->getAccount()->getAesKey(),
                $this->getAccount()->getAppId()
            );
        }
        return $this->encryptor;
    }

    /**
     * @return AccountInterface
     * @author Weida
     */
    public function getAccount(): AccountInterface
    {
        if (!$this->account){
            $this->account = new Account(
                $this->config->get('app_id'),
                $this->config->get('secret'),
                $this->config->get('token'),
                $this->config->get('aes_key'),
            );
        }
        return $this->account;
    }

    /**
     * @return ResponseInterface
     * @author Weida
     */
    public function getResponse():ResponseInterface
    {
        if(empty($this->response)){
            $this->response = new Response(
                $this->getRequest(),
                $this->getEncryptor()
            );
        }
        $this->getResponseAfter();
        return $this->response;
    }

    /**
     * @return ResponseInterface
     * @author Weida
     */
    public function getServer():ResponseInterface{
        return $this->getResponse();
    }

    protected function getResponseAfter(){}
}
