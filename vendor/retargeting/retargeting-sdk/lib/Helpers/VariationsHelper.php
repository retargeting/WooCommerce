<?php
/**
 * Created by PhpStorm.
 * User: andreicotaga
 * Date: 2019-03-18
 * Time: 12:46
 */

namespace RetargetingSDK\Helpers;

final class VariationsHelper extends AbstractHelper implements Helper
{
    /**
     * Format variations object
     * @param mixed $variation
     * @return array|\stdClass
     */
    public static function validate($variation)
    {
        $variationArr = [
            'variations' => false,
            'stock' => []
        ];

        if(is_array($variation))
        {
            if(array_key_exists('variations', $variation) && isset($variation['variations']))
            {
                $variationArr['variations'] = $variation['variations'];
            }
            else
            {
                $variationArr['variations'] = false;
            }

            if(array_key_exists('stock', $variation)  && isset($variation['stock']))
            {
                $variationArr['stock'] = $variation['stock'];
            }
        }

        return $variationArr;
    }
}