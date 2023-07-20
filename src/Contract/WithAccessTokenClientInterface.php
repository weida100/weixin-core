<?php
declare(strict_types=1);

/**
 * Author: Weida
 * Date: 2023/7/20 18:13 PM
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinCore\Contract;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;

interface WithAccessTokenClientInterface
{
    public function request(string $method, string $uri, array $options = []): ResponseInterface;
    public function get(string $uri, array $options = []): ResponseInterface;
    public function put(string $uri, array $options = []): ResponseInterface;
    public function post(string $uri, array $options = []): ResponseInterface;
    public function delete(string $uri, array $options = []): ResponseInterface;
    public function postJson(string $uri, array $options = []):ResponseInterface;
    public function postXml(string $uri, array $options = []): ResponseInterface;
    public function patchJson(string $url, array $options = []): ResponseInterface;

    public function getAsync(string $uri, array $options = []): PromiseInterface;
    public function putAsync(string $uri, array $options = []): PromiseInterface;
    public function patchAsync(string $uri, array $options = []): PromiseInterface;
    public function deleteAsync(string $uri, array $options = []): PromiseInterface;
    public function postAsync(string $uri, array $options = []): PromiseInterface;

}