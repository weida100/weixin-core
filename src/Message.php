<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/22 21:49
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinCore;

class Message
{
    //接受消息类型
    const TYPE_TEXT = "text";
    const TYPE_IMAGE = "image";
    const TYPE_VOICE = 'voice';
    const TYPE_VIDEO = 'video';
    /**
     * @var string 被动回复时
     */
    const TYPE_MUSIC = 'music';
    const TYPE_SHORTVIDEO = 'shortvideo';
    const TYPE_LOCATION = 'location';
    const TYPE_LINK = 'link';
    /**
     * @var string 被动回复时图文
     */
    const TYPE_NEWS = 'news'; //

    //授受事件类型
    const EVENT_SUBSCRIBE = 'subscribe';
    const EVENT_UNSUBSCRIBE = 'unsubscribe';
    const EVENT_SCAN = 'scan';
    const EVENT_LOCATION = 'location';
    const EVENT_CLICK = 'click';
    const EVENT_VIEW = 'view';
    const EVENT_TEMPLATESENDJOBFINISH = 'templatesendjobfinish';


    /**
     * @param string $msgType
     * @param string $event
     * @return string|null
     * @author Sgenmi
     */
    public static function getTypeVal(string $msgType,string $event=''):?string{
        if($msgType=='event'){
           $_defined = strtoupper($msgType.'_'.$event);
        }else{
            $_defined = strtoupper('type_'.$msgType);
        }
        $localConst = 'self::'.$_defined;
        if(!defined($localConst)){
            return null;
        }
        return strval(constant($localConst));
    }

}
