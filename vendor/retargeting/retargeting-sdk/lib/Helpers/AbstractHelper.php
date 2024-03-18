<?php
/**
 * Created by PhpStorm.
 * User: andreicotaga
 * Date: 2019-03-18
 * Time: 11:17
 */

namespace RetargetingSDK\Helpers;

class AbstractHelper
{
    /**
     * Sanitize a single var according to $type.
     * @param $var
     * @param $type
     * @return mixed
     */
    public static function sanitize($var, $type)
    {
        switch($type)
        {
//            case 'url':
//                $filter = FILTER_SANITIZE_URL;
//                break;
            case 'int':
                $filter = FILTER_SANITIZE_NUMBER_INT;
                break;
            case 'float':
                $filter = FILTER_SANITIZE_NUMBER_FLOAT;
                break;
            case 'email':
                $var = substr($var, 0, 254);
                $filter = FILTER_SANITIZE_EMAIL;
                break;
            case 'string':
            default:
                // $filter = FILTER_SANITIZE_STRING;
                return htmlspecialchars($var);
                break;

        }

        $result = filter_var($var, $filter);

        return $result;
    }

    /**
     * Format string
     * @param $string
     * @return string
     */
    public static function formatString($string)
    {
        $string = trim(strip_tags((string)$string));

        return $string;
    }

    /**
     * Filter an array by key
     * @param $array
     * @param $keyname
     * @return array
     */
    public static function filterArrayByKey($array, $keyname)
    {
        $new_array = [];

        foreach($array as $key => $value) {

            if(!isset($new_array[$value[$keyname]])) {
                $new_array[$value[$keyname]] = $value;
            }
        }

        $new_array = array_values($new_array);

        return $new_array;
    }

    /**
     * Throw exceptions when validating data
     * @param $message
     * @throws \Exception
     */
    public static function _throwException($message)
    {
        $messages = [
            "emptyURL" => "Url is required. Please don't leave it empty.",
            "wrongUrl" => "The url has wrong format.",
            "emptyCustomerData" => "Customer data is required. Please don't leave it empty.",
            "emptyToken" => "Token is required. Please don't leave it empty.",
            "wrongFormatToken" => "Token format is wrong.",
            "wrongFormat" => "The array format you provided is wrong.",
            "invalidEmail" => "Invalid email format.",
            "wrongPrice" => "Wrong price format."
        ];

        throw new \Exception($messages[$message]);
    }
}