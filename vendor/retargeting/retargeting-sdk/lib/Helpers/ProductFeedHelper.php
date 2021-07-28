<?php
/**
 * Created by PhpStorm.
 * User: andreicotaga
 * Date: 2019-03-18
 * Time: 17:56
 */

namespace RetargetingSDK\Helpers;

final class ProductFeedHelper extends AbstractHelper implements Helper
{
    /**
     * Check if product feed json is valid or not
     * @param mixed $feed
     * @return array|mixed
     */
    public static function validate($feed)
    {
        return $feed;
    }

    /**
     * Formats price into format, e.g. 1000.99.
     * @param $price
     * @return float
     * @throws \Exception
     */
    public static function formatPrice($price)
    {
        if(!is_numeric($price))
        {
            self::_throwException('wrongPrice');
        }

        return floatval(round($price, 2));
    }
}