<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/19 23:05
 * Email: weida_dev@163.com
 */

namespace Weida\WeixinCore;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\ResponseInterface;
use Weida\WeixinCore\Contract\HttpClientInterface;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;


class HttpClient extends Client implements HttpClientInterface
{
    private int $delay=1000;
    private float $multiplier=0.1;
    private int $maxRetries=3;
    private array $httpCodes=[429, 500];
    public function __construct(array $config = [])
    {
        $config = $this->_setRetry($config);
        parent::__construct($config);
    }

    private function _setRetry(array $config):array{
        if(!isset($config['retry']) || $config['retry']){
            if(!isset($config['handler'])){
                $config['handler'] = $this->retryHandler();
            }
            if(isset($config['retry']) && is_array($config['retry'])){
                $delay = intval($config['retry']['delay']??$this->delay);
                if($delay>=0){
                    $this->delay=$delay;
                }
                $maxRetries = intval($config['retry']['max_retries']??$this->maxRetries);
                if($maxRetries>0){
                    $this->maxRetries=$maxRetries;
                }
                $multiplier = floatval($config['retry']['multiplier']??$this->multiplier);
                if($multiplier>0){
                    $this->multiplier=$multiplier;
                }
                $httpCodes = $config['retry']['http_codes']??$this->httpCodes;
                if(is_array($httpCodes) ){
                    $httpCodes = array_filter(array_unique($httpCodes));
                    if($httpCodes){
                        $this->httpCodes = $httpCodes;
                    }
                }
                $filterCodes=[200,302];
                $this->httpCodes = array_map(function ($v)use($filterCodes){
                    if(in_array($v,$filterCodes)){
                        return null;
                    }
                },$this->httpCodes);
            }
        }
        unset($config['retry']);
        return  $config;
    }


    public function request(string $method, $uri = '', array $options = []): ResponseInterface
    {
        return parent::request($method, $uri, $options);
    }

    /**
     * retry
     * @return HandlerStack
     * @author Weida
     */
    protected function retryHandler():HandlerStack{
        $handlerStack = HandlerStack::create(new CurlHandler());
        $handlerStack->push(Middleware::retry($this->retryDecider(), $this->retryDelay()));
        return $handlerStack;
    }

    protected function retryDecider():callable{
        return function ( $retries, Request $request, ?Response $response = null, ?RequestException $exception = null) {
            // 超过最大重试次数，不再重试
            if ($retries >= $this->maxRetries) {
                return false;
            }
            // 请求失败，继续重试
            if ($exception instanceof ConnectException) {
                return true;
            }
            if ($response) {
                if (in_array($response->getStatusCode(),$this->httpCodes)) {
                    return true;
                }
            }
            return false;
        };
    }

    protected function retryDelay():callable{
        return function ($numberOfRetries) {
            return $this->delay * $this->multiplier;
        };
    }


}
