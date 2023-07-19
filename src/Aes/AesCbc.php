<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/19 23:08
 * Email: weida_dev@163.com
 */

namespace Weida\WeixinCore\Aes;

use Exception;
use Weida\WeixinCore\Contract\AesInterface;

class AesCbc implements AesInterface
{
    const AES128="aes-128-cbc";
    const AES256="aes-256-cbc";

    private string $cipherAlgo = "";
    public function __construct(string $cipherMethod)
    {
        $this->cipherAlgo = $cipherMethod;
    }


    /**
     * @param string $data
     * @param string $passphrase
     * @param int $options
     * @param string $iv
     * @return string
     * @throws Exception
     */
    public function encrypt(string $data, string $passphrase, int $options = 0, string $iv = ""): string
    {
        $cipherText = \openssl_encrypt($data, $this->cipherAlgo, $passphrase, $options, $iv);
        if (empty($cipherText)) {
            throw new Exception(openssl_error_string());
        }
        return $cipherText;
    }

    /**
     * @param string $data
     * @param string $passphrase
     * @param int $options
     * @param string $iv
     * @return string
     * @throws Exception
     * @author Weida
     */
    public function decrypt(string $data, string $passphrase, int $options = 0, string $iv = ""): string
    {
        $plainText = openssl_decrypt( $data, $this->cipherAlgo, $passphrase, OPENSSL_RAW_DATA, $iv);
        if (empty($plainText)) {
            throw new Exception(openssl_error_string());
        }
        return $plainText;
    }
}
