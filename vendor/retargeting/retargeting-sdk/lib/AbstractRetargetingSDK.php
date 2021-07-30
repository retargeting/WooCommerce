<?php
/**
 * Created by PhpStorm.
 * User: bratucornel
 * Date: 2019-02-19
 * Time: 07:48
 */

namespace RetargetingSDK;

use RetargetingSDK\Helpers\UrlHelper;

/**
 * Class AbstractRetargetingSDK
 */
abstract class AbstractRetargetingSDK
{
    /**
     * @param array $data
     * @return string
     */
    public function toJSON(array $data)
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * Get proper formatted string
     * @param $text
     * @return string
     */
    public function getProperFormattedString($text)
    {
        $text = (string)$text;

        if ((bool)$text) {
            return trim(strip_tags(html_entity_decode(
                html_entity_decode($text),
                ENT_QUOTES | ENT_COMPAT,
                'UTF-8')));
        } else {
            return '';
        }
    }

    /**
     * Parse correct format of given data
     * @param $value
     * @return float|int|string
     */
    public function formatIntFloatString($value)
    {

        if(!is_numeric($value))
        {
            return 0;
        }

        if($this->isFloat($value))
        {
            return (float)$value;
        }
        else if(!$this->isFloat($value))
        {
            return (int)$value;
        }
        else
        {
            return $this->getProperFormattedString($value);
        }
    }

    /**
     * Is value float or not
     * @param $num
     * @return bool
     */
    function isFloat($num)
    {
        return is_float($num) || is_numeric($num) && ((float) $num != (int) $num);
    }

    /**
     * Format url from an array
     * @param $array
     * @return array
     */
    public function validateArrayData($array)
    {
        $mappedArray = array_map(function($item){
            return UrlHelper::validate($item);
        }, $array);

        return $mappedArray;
    }
}