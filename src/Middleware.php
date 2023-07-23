<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/21 23:10
 * Email: weida_dev@163.com
 */

namespace Weida\WeixinCore;
use InvalidArgumentException;

final class Middleware
{
    protected array $handlers=[];
    public function __construct()
    {
    }

    public function addHandler(callable|string|array|object $handler,string $msgType=''):void{
        if(!empty($msgType)){
            $this->handlers[$msgType] = $this->parseHanlder($handler);
        }else{
            $this->handlers[] = $this->parseHanlder($handler);
        }
    }

    protected function parseHanlder(callable|string|array|object $handler){
        if(empty($handler)){
            throw new \InvalidArgumentException('invalid handler');
        }
        if(is_callable($handler)){
            return $handler;
        }
        if (is_string($handler) && class_exists($handler) && method_exists($handler, '__invoke')) {
            if(method_exists('handler')){
                return [new $handler(),'handler'];
            }
            if(method_exists($handler,'__invoke')){
                return fn (): mixed => (new $handler())(...func_get_args());
            }
        }
        //
        $obj=null;
        $action='handler';
        if(is_array($handler)){
            $cls = $handler[0]??'';
            $action = $handler[1]??'';
            if(empty($cls)||empty($action)){
                throw new \InvalidArgumentException(sprintf('invalid handler 1 [%s]',json_encode($handler)));
            }
            if(is_string($cls)){
                $handler = $cls.'::'.$action;
            }{
                $obj= $cls;
            }
        }
        if(is_string($handler)){
            if(str_contains($handler,'::')){
                $handlerArr = array_filter(explode('::',$handler));
                $cls = $handlerArr[0]??'';
                $action = $handlerArr[1]??'';
                if(!$cls || !$action){
                    throw new \InvalidArgumentException(sprintf('invalid handler 2 [%s]',$handler));
                }
            }else{
                $cls = $handler;
            }
            $obj = new $cls();
        }elseif (is_object($handler)){
            $obj = $handler;
        }
        if($obj){
            if(method_exists($obj,'handler')){
                var_dump(00000000);
                return [$obj,'handler'];
            }
            if(method_exists($obj,'__invoke')){
                return fn (): mixed => ($obj)(...func_get_args());
            }
        }
        throw new InvalidArgumentException(sprintf('Invalid handler 3 [%s]', is_object($handler)?get_class($handler):$handler));
    }

    public function handler($result ,array|string|null $message=null){
        $next = $result = is_callable($result) ? $result : fn (mixed $p): mixed => $result;
        $handlers = array_reverse($this->handlers);
        //todo $msgType
        $msgType = Message::getTypeVal($message['MsgType']??'',$message['Event']??'');
        foreach ($handlers as $k=> $v) {
            if(is_string($k)){
                if($k===$msgType){
                    $next = fn (mixed $p): mixed => $v($p, $next) ?? $result($p);
                }
                continue;
            }
            $next = fn (mixed $p): mixed => $v($p, $next) ?? $result($p);
        }
        return $next($message);
    }


}
