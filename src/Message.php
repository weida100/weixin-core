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
     * 客户消息时 发送图文消息（点击跳转到外链） 图文消息条数限制在1条以内，注意，如果图文数超过1，则将会返回错误码45008。
     *  被动回复 可以8条
     * @var string 被动回复时图文
     */
    const TYPE_NEWS = 'news'; //
    //发送图文消息
    const TYPE_MPNEWSARTICLE = 'mpnewsarticle';
    //发送菜单消息
    const TYPE_MSGMENU = 'msgmenu';
    //发送卡券
    const TYPE_WXCARD = 'wxcard';

    //授受事件类型
    const EVENT_SUBSCRIBE = 'subscribe';
    const EVENT_UNSUBSCRIBE = 'unsubscribe';
    const EVENT_SCAN = 'scan';
    const EVENT_LOCATION = 'location';
    const EVENT_CLICK = 'click';
    const EVENT_VIEW = 'view';
    const EVENT_TEMPLATESENDJOBFINISH = 'templatesendjobfinish';

    const OPEN_COMPONENT_VERIFY_TICKET="open_component_verify_ticket";
    const OPEN_AUTHORIZED='open_authorized';
    const OPEN_UPDATEAUTHORIZED = 'open_updateauthorized';
    const OPEN_UNAUTHORIZED = 'open_unauthorized';

    /**
     * @param string $msgType
     * @param string $event
     * @param string $appType officialAccount|openPlatform|work|workOpenPlatform|miniApp|videoShop
     * @return string|null
     * @author Sgenmi
     */
    public static function getTypeVal(string $msgType,string $event='',string $appType='officialAccount'):?string{
        $_defined="weida";
        switch ($appType){
            case 'officialAccount':
            case 'miniApp':
            case 'videoShop':
                if($msgType=='event'){
                    $_defined = strtoupper($msgType.'_'.$event);
                }else{
                    $_defined = strtoupper('type_'.$msgType);
                }
                break;
            case 'openPlatform':
                $_defined = strtoupper('open_'.$msgType);
                break;

            case 'work':
                $_defined = strtoupper('work_'.$msgType);
                break;
            case 'workOpenPlatform':
                $_defined = strtoupper('work_open_'.$msgType);
                break;
        }
        $localConst = 'self::'.$_defined;
        if(!defined($localConst)){
            return null;
        }
        return strval(constant($localConst));
    }

}
