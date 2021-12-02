<?php
namespace Plugin;
use PHF\Exception;

/**
 * DES加密类
 */
class DesEncrypt {
    private $key = "";
    private $iv = "";

    /**
     * @param string $key
     * @param string $iv
     */
    function __construct (string $key, string $iv)
    {
        if (empty($key) || empty($iv)) {
            Exception::throw('key and iv is not valid',100);
        }
        $this->key = $key;
        $this->iv = $iv;//8
        //$this->iv = $iv.'00000000000';//16

    }

    /**
     * 加密
     * @param string $value 要加密的数据
     * @return string|false
     */
    public function encrypt (string $value) {

        //参考地址：https://stackoverflow.com/questions/41181905/php-mcrypt-encrypt-to-openssl-encrypt-and-openssl-zero-padding-problems#
        $value = $this->PaddingPKCS7($value);
        $key = $this->key;
        $iv  = $this->iv;
        //AES-128-ECB|不能用 AES-256-CBC|16 AES-128-CBC|16 BF-CBC|8 aes-128-gcm|需要加$tag  DES-EDE3-CBC|8
        $cipher = "DES-EDE3-CBC";
        $result = openssl_encrypt($value, $cipher, $key, OPENSSL_SSLV23_PADDING, $iv);
        return $result === false ? false : base64_encode($result);

    }

    /**
     * 解密
     * @param string $value 要解密的数据
     * @return string|false
     */
    public function decrypt (string $value) {
        $key       = $this->key;
        $iv        = $this->iv;
        $decrypted = openssl_decrypt(base64_decode($value), 'DES-EDE3-CBC', $key, OPENSSL_SSLV23_PADDING, $iv);
        return $this->UnPaddingPKCS7($decrypted);
    }

    private function PaddingPKCS7 ($data): string
    {
        //$block_size = mcrypt_get_block_size('tripledes', 'cbc');//获取长度
        //$block_size = openssl_cipher_iv_length('tripledes', 'cbc');//获取长度
        $block_size = 8;
        $padding_char = $block_size - (strlen($data) % $block_size);
        $data .= str_repeat(chr($padding_char), $padding_char);
        return $data;
    }

    private function UnPaddingPKCS7($text) {
        $pad = ord($text[strlen($text) - 1]);
        if ($pad > strlen($text)) {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }
        return substr($text, 0, - 1 * $pad);
    }
}