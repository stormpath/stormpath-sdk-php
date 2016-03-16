<?php

namespace Stormpath\Authc\Api;

use phpseclib\Crypt\AES as ModernAES;
use Crypt_AES as OldAES;

/*
 * Copyright 2016 Stormpath, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

class ApiKeyEncryptionUtils
{
    public static function decrypt($secret, $password, ApiKeyEncryptionOptions $options)
    {
        $decodedSecret = self::base64url_decode($secret);
        $salt = self::base64url_decode($options->getEncryptionKeySalt());
        $iterations = $options->getEncryptionKeyIterations();
        $keyLengthBits = $options->getEncryptionKeySize();
        $iv = substr($decodedSecret, 0, 16);

        if (class_exists('phpseclib\Crypt\AES')) {
            $aes = new ModernAES();
        } else {
            $aes = new OldAES();
        }

        $aes->setPassword($password, 'pbkdf2', 'sha1', $salt, $iterations, $keyLengthBits / 8);
        $aes->setKeyLength($keyLengthBits);
        $aes->setIV($iv);

        return $aes->decrypt(substr($decodedSecret, 16));
    }

    public static function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64url_decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'),
            strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    /**
     * http://www.php.net/manual/en/ref.mcrypt.php#69782
     *
     * @param $text
     * @param $blocksize
     * @return string
     */
    private function pkcs5_pad ($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    /**
     * http://www.php.net/manual/en/ref.mcrypt.php#69782
     *
     * @param $text
     * @param $blocksize
     * @return string
     */
    private function pkcs5_unpad($text)
    {
        $pad = ord($text{strlen($text)-1});
        if ($pad > strlen($text)) return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
        return substr($text, 0, -1 * $pad);
    }
}
