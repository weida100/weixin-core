<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/19 23:02
 * Email: weida_dev@163.com
 */

namespace Weida\WeixinCore;

use GuzzleHttp\ClientTrait;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Weida\WeixinCore\Contract\AccessTokenInterface;
use Weida\WeixinCore\Contract\WithAccessTokenClientInterface;

class WithAccessTokenClient implements WithAccessTokenClientInterface
{
    private HttpClient $httpClient;
    private AccessTokenInterface $accessToken;
    private bool $isThrow =false;

    public function __construct(HttpClient $httpClient,AccessTokenInterface $accessToken,bool $isThrow=false)
    {
        $this->httpClient = $httpClient;
        $this->accessToken = $accessToken;
        $this->isThrow = $isThrow;
    }

    public function get(string $uri, array $options = []): ResponseInterface
    {
       return $this->request('GET',$uri,$options);
    }

    public function put(string $uri, array $options = []): ResponseInterface
    {
        return $this->request('PUT',$uri,$options);
    }

    public function post(string $uri, array $options = []): ResponseInterface
    {
        return $this->request('POST',$uri,$options);
    }

    public function delete(string $uri, array $options = []): ResponseInterface
    {
        return $this->request('DELETE',$uri,$options);
    }

    public function postJson(string $uri, array $options = []): ResponseInterface
    {
        return $this->request('POST',$uri,$options);
    }

    public function postXml(string $uri, array $options = []): ResponseInterface
    {
        return $this->request('POST',$uri,$options);
    }

    public function patchJson(string $url, array $options = []): ResponseInterface
    {
        return $this->request('PATCH',$uri,$options);
    }

    public function getAsync(string $uri, array $options = []): PromiseInterface
    {
        return $this->requestAsync('GET',$uri,$options);
    }

    public function putAsync(string $uri, array $options = []): PromiseInterface
    {
        return $this->requestAsync('PUT',$uri,$options);
    }

    public function patchAsync(string $uri, array $options = []): PromiseInterface
    {
        return $this->requestAsync('PATCH',$uri,$options);
    }

    public function deleteAsync(string $uri, array $options = []): PromiseInterface
    {
        return $this->requestAsync('DELETE',$uri,$options);
    }

    public function postAsync(string $uri, array $options = []): PromiseInterface
    {
        return $this->requestAsync('POST',$uri,$options);
    }

    public function request(string $method, string $uri, array $options = []): ResponseInterface
    {
        $uri = $this->withAccessToken($uri).'access_token='.$this->accessToken->getToken();
        return $this->httpClient->request($method,$uri,$options);
    }

    public function requestAsync(string $method, string $uri, array $options = []):PromiseInterface{
        $uri = $this->withAccessToken($uri).'access_token='.$this->accessToken->getToken();
        return $this->httpClient->requestAsync($method,$uri,$options);
    }

    /**
     * 这里为什么不直接加token,是为了当有时会出现token无效或过期了，被其他地方复盖
     * @param string $uri
     * @return string
     */
    private function withAccessToken(string $uri){
        return  $uri.(str_contains($uri,'?')?'&':"?");
    }

}
