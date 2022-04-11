<?php
/**
 * Created by PhpStorm.
 * User: andreicotaga
 * Date: 2019-03-22
 * Time: 11:02
 */

namespace RetargetingSDK\Helpers;

final class CustomersApiHelper extends AbstractHelper implements Helper
{
    const CUSTOMER_DATA_KEYS = [
        'firstName',
        'lastName',
        'email',
        'phone',
        'status'
    ];

    /**
     * Validate customer data
     * @param $data
     * @return array|mixed
     * @throws \Exception
     */
    public static function validate($data)
    {
        $customers = [];

        if(empty($data))
        {
            self::_throwException('emptyCustomerData');
        }

        if(!empty($data))
        {
            $keys = array_keys($data[0]);

            $keysDiff = array_diff(self::CUSTOMER_DATA_KEYS, $keys);

            if(count($keysDiff) > 0)
            {
                self::_throwException('wrongFormat');
            }

            $customers = $data;
        }

        return $customers;
    }

    /**
     * Get token
     * @param $token
     * @return mixed
     * @throws \Exception
     */
    public static function getToken($token)
    {
        if(empty($token))
        {
            self::_throwException('emptyToken');
        }

        return $token;
    }
}