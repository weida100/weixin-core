<?php
declare(strict_types=1);

/**
 * Author: Weida
 * Date: 2023/7/20 7:34 PM
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinCore;

use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Weida\WeixinCore\Contract\EncryptorInterface;
use Weida\WeixinCore\Contract\RequestInterface;
use Weida\WeixinCore\Contract\ResponseInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\MessageInterface as PsrMessageInterface;


abstract class AbstractResponse implements ResponseInterface
{
    protected RequestInterface|ServerRequestInterface $request;
    protected ?EncryptorInterface $encryptor=null;
    protected array $params=[];
    protected Middleware $middleware;
    public function __construct(
        RequestInterface|ServerRequestInterface $request,
        ?EncryptorInterface $encryptor=null,
        string $appType=''
        )
    {
        $this->request = $request;
        $this->encryptor = $encryptor;
        $this->params = $this->request->getQueryParams();
        $this->middleware = new Middleware($appType);
    }

    /**
     * @param callable|string|array|object $callback
     * @return $this
     * @author Sgenmi
     */
    public function with(callable|string|array|object $callback):static{
        $this->middleware->addHandler($callback);
        return $this;
    }

    /**
     * @param string $msgType
     * @param callable|string|array|object $handler
     * @return $this
     * @author Sgenmi
     */
    public function addMessageListener(string $msgType,callable|string|array|object $handler):static{
        $this->middleware->addHandler($handler,$msgType);
        return $this;
    }

    /**
     * @param string $msgType
     * @param callable|string|array|object $handler
     * @return $this
     * @author Sgenmi
     */
    public function addEventListener(string $msgType,callable|string|array|object $handler):static{
        $this->middleware->addHandler($handler,$msgType);
        return $this;
    }

    /**
     * @return string
     * @author Sgenmi
     */
    public function getRequestMessage():string{
        return $this->request->getBody()->getContents();
    }

    /**
     * @return array|string
     * @author Sgenmi
     */
    public function getDecryptedMessage(): array|string
    {
        $message = $this->getRequestMessage();
        if (empty($this->encryptor) || empty($this->params['msg_signature'])) {
            return $message;
        }
        $str = $this->encryptor->decrypt(
            $message,$this->params['msg_signature'],$this->params['nonce']??'',$this->params['timestamp']??0
        );
        return Xml::parse($str);
    }

    /**
     * @param string $body
     * @return StreamInterface
     * @author Weida
     */
    protected function createBody(string $body):StreamInterface
    {
        return Utils::streamFor($body);
    }

    /**
     * @return PsrResponseInterface
     * @author Weida
     */
    public function serve():PsrMessageInterface|PsrResponseInterface{
        return $this->response();
    }

}
