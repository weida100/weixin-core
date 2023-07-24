<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/19 22:59
 * Email: weida_dev@163.com
 */

namespace Weida\WeixinCore;
use RuntimeException;
use Weida\WeixinCore\Aes\AesCbc;
use Weida\WeixinCore\Contract\EncryptorInterface;

class Encryptor implements EncryptorInterface
{
    protected string $appId; //公众平台的appId
    protected string $token; //公众平台上，开发者设置的token
    protected string $encodingAesKey; //公众平台上，开发者设置的EncodingAESKey
    protected string $receiveId=''; //不同应用场景传不同的id
    public function __construct(string $appId, string $token, string $encodingAesKey,string $receiveId = '')
    {
        $this->appId = $appId;
        $this->token = $token;
        $this->encodingAesKey = base64_decode($encodingAesKey . "=");
    }

    /**
     * @param string $ciphertext
     * @param string $msgSignature
     * @param string $nonce
     * @param int|string $timestamp
     * @return string
     * @throws \Exception
     */
    public function decrypt(string $ciphertext, string $msgSignature, string $nonce, int|string $timestamp): string
    {
        //提取密文
        $array = XML::extract($ciphertext);
        $encrypt = $array['encrypt'];
        if (empty($encrypt)) {
            throw new RuntimeException('not fund encrypt');
        }
        //验证安全签名
        $signature = $this->getSHA1($this->token, $timestamp, $nonce, $encrypt);
        if ($signature != $msgSignature) {
            throw new RuntimeException('Invalid Signature.');
        }
        $plaintext = AesCbc::decrypt(
            base64_decode($encrypt, true) ?: '',
            AesCbc::AES256,
            $this->encodingAesKey,
            OPENSSL_NO_PADDING,
            substr($this->encodingAesKey, 0, 16)
        );
        $plaintext = Pkcs7Encoder::decode($plaintext);
        //去除16位随机字符串,网络字节序和AppId
        if (strlen($plaintext) < 16){
            throw new RuntimeException('decrpyt exception',-40007);
        }
        $content = substr($plaintext, 16);
        $lenList = unpack("N", substr($content, 0, 4))??[];
        $xmlLen = $lenList[1];
        $fromAppid = substr($content, $xmlLen + 4);
        if ($this->receiveId && trim($fromAppid) !== $this->receiveId) {
            throw new RuntimeException('Invalid appId.', -40005);
        }
        return substr($content, 4, $xmlLen);
    }

    /**
     * 公众号，有回复消息，则这里要加密后，回复出去
     * @param string $plaintext
     * @param string $nonce
     * @param int|string $timestamp
     * @return string
     */
    public function encrypt(string $plaintext, string $nonce = '', int|string $timestamp = ''): string
    {
        try {
            //获得16位随机字符串，填充到明文之前
            $random = $this->getRandomStr();
            $plaintext = $random . pack("N", strlen($plaintext)) . $plaintext . $this->appId;
            $plaintext = Pkcs7Encoder::encode($plaintext);
            $ciphertext = base64_encode(
                AesCbc::encrypt(
                    $plaintext,
                    AesCbc::AES256,
                    $this->encodingAesKey,
                    OPENSSL_NO_PADDING,
                    substr($this->encodingAesKey, 0, 16)
                )
            );
            $nonce = $nonce?: $this->getRandomStr(16);
            $timestamp = $timestamp?: time();
            $response = [
                'Encrypt' => $ciphertext,
                'MsgSignature' => $this->getSHA1($this->token, $timestamp, $nonce, $ciphertext),
                'TimeStamp' => $timestamp,
                'Nonce' => $nonce,
            ];
            return XML::generate($response);
        }catch (\Throwable $e){

        }
        return '';
    }

    private function getSHA1(string $token, int|string $timestamp, string $nonce, string $encryptMsg):string
    {
        //排序
        $array = [$encryptMsg, $token, $timestamp, $nonce];
        sort($array , SORT_STRING);
        return sha1(implode($array));
    }

    private function getRandomStr(int $length = 6, string $alphabet = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789'):string
    {
        if ($length >= strlen($alphabet)) {
            $rate = intval($length / strlen($alphabet)) + 1;
            $alphabet = str_repeat($alphabet, $rate);
        }
        return substr(str_shuffle($alphabet), 0, $length);
    }

}
