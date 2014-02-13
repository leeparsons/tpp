<?php
/**
 * User: leeparsons
 * Date: 25/01/2014
 * Time: 16:33
 */

class TppStoreLibraryEncryption {

    private $salt = 'Tx5IVnkxo6Ao6';

    public function encrypt($str = '', $human_readable = true)
    {


        $encrypted = mcrypt_encrypt(
            MCRYPT_RIJNDAEL_256,
            $this->salt,
            $str,
            MCRYPT_MODE_ECB,
            mcrypt_create_iv(
                mcrypt_get_iv_size(
                    MCRYPT_RIJNDAEL_256,
                    MCRYPT_MODE_ECB
                ),
                MCRYPT_RAND
            )
        );

        if ($human_readable === true) {
            return base64_encode($encrypted);
        } else {
            return $encrypted;
        }

    }


    public function decrypt($str = '', $human_readable = true)
    {

        if ($human_readable === true) {
            $str = base64_decode($str);
        }

        return trim(
            mcrypt_decrypt(
                MCRYPT_RIJNDAEL_256,
                $this->salt,
                $str,
                MCRYPT_MODE_ECB,
                mcrypt_create_iv(
                    mcrypt_get_iv_size(
                        MCRYPT_RIJNDAEL_256,
                        MCRYPT_MODE_ECB
                    ),
                    MCRYPT_RAND
                )
            )
        );
    }


}