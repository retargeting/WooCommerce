<?php
/**
 * Created by PhpStorm.
 * User: bratucornel
 * Date: 2019-03-13
 * Time: 13:40
 */

namespace RetargetingSDK\Helpers;

/**
 * Class Token
 * @package Retargeting\Helpers
 */
final class TokenHelper
{
    /**
     * This method will be used to generate user token right after module setup. The key must be saved in
     * Retargeting admin account and on client website.
     *
     * @param $domain
     * @param $raJs
     * @param $rarestApi
     * @return string
     */
    public static function createUserToken($domain, $raJs, $rarestApi)
    {
        $content = strtolower($domain . $raJs . $rarestApi);

        return hash('sha256', $content);
    }
}
