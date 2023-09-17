<?php
declare(strict_types=1);

/**
 * Author: Sgenmi
 * Date: 2023/7/21 5:48 PM
 * Email: 150560159@qq.com
 */

namespace Weida\WeixinCore;

class Xml
{

    /**
     * 提取出xml数据包中的加密消息
     * @param string $xmltext 待提取的xml字符串
     * @return array 提取出的加密消息字符串
     */
    public static function extract(string $xmltext)
    {
        if(PHP_VERSION_ID<80000){
            libxml_disable_entity_loader(true);
        }
        $xml = new \DOMDocument();
        $xml->loadXML($xmltext);
        $array_e = $xml->getElementsByTagName('Encrypt');
        $array_a = $xml->getElementsByTagName('ToUserName');
        $encrypt = '';
        if ($array_e->length) {
            $encrypt = $array_e->item(0)->nodeValue;
        }
        $tousername = '';
        if ($array_a->length) {
            $tousername = $array_a->item(0)->nodeValue;
        }
        return [
            'encrypt'=>$encrypt,
            'tousername'=>$tousername
        ];
    }

    /**
     * @param array $params
     * @return string
     * @author Sgenmi
     */
    public static function generate(array $params):string{
        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->setIndent(false);
        $xml->startDocument();
        $xml->startElement('xml');
        foreach ($params as $key => $value) {
            $xml->writeElement($key, (string)$value);
        }
        $xml->endElement();
        $xml->endDocument();
        $xmlStr =  $xml->outputMemory();
        $xml=null;
        return $xmlStr;
    }

    /**
     * @param string $xml
     * @return array
     * @author Sgenmi
     */
    public static function parse(string $xml): array
    {
        if (str_starts_with($xml, '<')) {
            if(PHP_VERSION_ID<80000){
                libxml_disable_entity_loader(true);
            }
            $xml = json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA));
        }
        $arr = json_decode($xml, true);
        if (empty($arr) || ! is_array($arr)) {
            throw new \RuntimeException('Failed to decode request contents.');
        }
        return $arr;
    }

}
