<?php
/**
 * Created by PhpStorm.
 * User: andreicotaga
 * Date: 2019-03-18
 * Time: 14:43
 */

namespace RetargetingSDK\Helpers;

final class EmailHelper extends AbstractHelper implements Helper
{
    /**
     * Validate email
     * @param $email
     * @return mixed
     * @throws \Exception
     */
    public static function validate($email)
    {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            self::_throwException('invalidEmail');
        }

        $email = self::sanitize($email, 'email');

        return $email;
    }

    /**
     * Validate user birthday
     * @param $birthday
     * @return \DateTime|string
     * @throws \Exception
     */
    public static function validateBirthday($birthday)
    {
        $dob = '';

        if(self::isDate($birthday))
        {
            $dob = new \DateTime($birthday);

            return $dob->format('d-m-Y');
        }

        return $dob;
    }

    /**
     * Check if the value is a valid date
     * @param mixed $value
     * @return boolean
     */
    public static function isDate($value)
    {
        if (!$value) {
            return false;
        } else {
            $date = date_parse($value);
            if($date['error_count'] == 0 && $date['warning_count'] == 0) {
                return checkdate($date['month'], $date['day'], $date['year']);
            } else {
                return false;
            }
        }
    }
}