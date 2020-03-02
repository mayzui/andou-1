<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2019-02-27
 * Time: 15:26
 */

namespace App\Common\Utils;

use phpseclib\Crypt\{AES, DES, Hash, RC2, RC4, RSA};

/**
 * Class Crypt
 *
 * @package App\Common\Utils
 */
class Crypt {

    public static function AES($keyLength = 128, $key = '', $mode = AES::MODE_CTR) {
        $aes = new AES();
        $aes->mode = $mode;
        $key = $key ?: env('APP_KEY');
        $aes->setKey($key);
        $aes->setKeyLength($keyLength);
        return $aes;
    }

    public static function DES() {
        return new DES();
    }

    public static function RC2() {
        return new RC2();
    }

    public static function RC4() {
        return new RC4();
    }

    public static function RSA() {
        return new RSA();
    }

    /**
     * Hash 字符串处理
     *
     * @param string $str 需处理的字符串
     * @param string $algo 加密方式
     * @param string $key 加密 Key，默认使用框架设置的 APP_KEY
     * @param bool   $hex 是否转换并返回 Hex，默认为 true
     *
     * @return string 返回处理后的 Hash 值
     */
    public static function hash($str, $algo = 'sha256', $key = null, $hex = true) {
        $hash = new Hash($algo);
        $hash->setKey($key ?: env('APP_KEY'));
        $hashed = $hash->hash($str);
        if ($hex) {
            if (function_exists('sodium_bin2hex')) {
                return sodium_bin2hex($hashed);
            }
            return bin2hex($hashed);
        }
        return $hashed;
    }
}
