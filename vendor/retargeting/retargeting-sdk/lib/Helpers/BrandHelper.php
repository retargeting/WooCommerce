<?php
/**
 * Created by PhpStorm.
 * User: andreicotaga
 * Date: 2019-03-18
 * Time: 11:57
 */

namespace RetargetingSDK\Helpers;

final class BrandHelper extends AbstractHelper implements Helper
{
    /**
     * Format brand object
     * @param mixed $brand
     * @return array|bool|\stdClass
     */
    public static function validate($brand)
    {
        if(is_array($brand))
        {
            $brandArr = [];

            if(array_key_exists('id', $brand) && isset($brand['id']))
            {
                $brandArr['id'] = $brand['id'];
            }
            else
            {
                return false;
            }

            if(array_key_exists('name', $brand)  && isset($brand['name']))
            {
                $brandArr['name'] = self::sanitize($brand['name'], 'string');
            }

            return $brandArr;
        }

        return false;
    }
}