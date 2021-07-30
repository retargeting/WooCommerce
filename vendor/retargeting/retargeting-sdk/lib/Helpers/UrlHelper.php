<?php
/**
 * Created by PhpStorm.
 * User: andreicotaga
 * Date: 2019-03-15
 * Time: 10:50
 */

namespace RetargetingSDK\Helpers;

final class UrlHelper extends AbstractHelper implements Helper
{
    const HTTP_PROTOCOLS = ['https', 'http'];
    const HTTPS_VALUE = 'https://';

    /**
     * Check if url contains https/http
     * @param $url
     * @return mixed
     * @throws \Exception
     */
    public static function validate($url)
    {
        if(empty($url))
        {
            self::_throwException('emptyURL');
        }

        $url = self::formatString($url);

        $parsedUrl = parse_url($url);

        if(!array_key_exists('host', $parsedUrl))
        {
            self::_throwException('wrongUrl');
        }

        if(isset($parsedUrl['scheme']) && !in_array($parsedUrl['scheme'], self::HTTP_PROTOCOLS))
        {
            $url = self::prepend(filter_input(INPUT_GET, 'link', FILTER_SANITIZE_URL), self::HTTPS_VALUE) . $parsedUrl['path'] . '?' . $parsedUrl['query'];
        }

        return self::sanitize($url, 'url');
    }

    /**
     * Prepend a string at the start of the initial string
     * @param $string
     * @param $prefix
     */
    public static function prepend(& $string, $prefix)
    {
        $string = substr_replace($string, $prefix, 0, 0);
    }
}