<?php
/**
 * Created by PhpStorm.
 * User: bratucornel
 * Date: 2019-03-13
 * Time: 12:25
 */

namespace RetargetingSDK\Helpers;

/**
 * Class Encryption
 * @package Retargeting\Helpers
 */
class EncryptionHelper
{
    /**
     * Encryption method
     */
    const METHOD = "AES-256-CBC";

    /**
     * Hash algorithm
     */
    const HASH_ALGORITHM = 'sha512';

    /**
     * @var string
     */
    protected $token;

    /**
     * EncryptionHelper constructor.
     * @param $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * @param mixed $data
     * @return string
     */
    public function encrypt($data)
    {
        $ivSize = openssl_cipher_iv_length(self::METHOD);
        $iv = openssl_random_pseudo_bytes($ivSize);

        $encrypted = openssl_encrypt($data, self::METHOD, $this->createKey(), OPENSSL_RAW_DATA, $iv);

        return rtrim(strtr(base64_encode($iv . $encrypted), '+/', '-_'), '=');
    }

    /**
     * @return string
     */
    private function createKey()
    {
        return hash(self::HASH_ALGORITHM, $this->token);
    }
}
