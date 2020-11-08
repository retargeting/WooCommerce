<?php
/**
 * Created by PhpStorm.
 * User: andreicotaga
 * Date: 2019-03-18
 * Time: 12:09
 */

namespace RetargetingSDK\Helpers;

final class CategoryHelper extends AbstractHelper implements Helper
{
    /**
     * Format product category
     * @param mixed $categoryData
     * @return array|\stdClass
     */
    public static function validate($categoryData)
    {
        $categoryArr = [];

        if(is_array($categoryData) && !empty($categoryData))
        {
            //Check if there are duplicated parent categories
            $categoryData = self::filterArrayByKey($categoryData, 'parent');

            //Get the first category if there is only one
            if(count($categoryData) < 2)
            {
                $categoryArr[] = [
                    'id'         => $categoryData[0]['id'],
                    'name'       => self::formatString($categoryData[0]['name']),
                    'parent'     => false,
                    'breadcrumb' => empty($categoryData[0]['breadcrumb']) ? [] : self::validateBreadcrumb($categoryData[0]['breadcrumb'])
                ];

                /*$categoryArr['id']          = $categoryData[0]['id'];
                $categoryArr['name']        = self::formatString($categoryData[0]['name']);
                $categoryArr['parent']      = false;
                $categoryArr['breadcrumb']  = empty($categoryData[0]['breadcrumb']) ? [] : self::validateBreadcrumb($categoryData[0]['breadcrumb']);*/
            }
            //Check if there are nested categories
            else if (count($categoryData) >= 2)
            {
                foreach($categoryData as $category)
                {
                    $category['breadcrumb'] = self::filterArrayByKey($category['breadcrumb'], 'parent');

                    $category['name'] = self::formatString($category['name']);
                    $category['breadcrumb'] = is_array($category['breadcrumb']) ? $category['breadcrumb'] : (array)$category['breadcrumb'];

                    $categoryArr[]        = $category;
                }
            }
        }

        return $categoryArr;
    }

    /**
     * @param $breadcrumb
     * @return array
     */
    public static function validateBreadcrumb($breadcrumb)
    {
        $breadcrumbArr = [];

        if(is_array($breadcrumb) && !empty($breadcrumb))
        {
            if(array_key_exists('0', $breadcrumb))
            {
                foreach ($breadcrumb as $value)
                {
                    $value['id']        = self::formatString($value['id']);
                    $value['name']      = self::formatString($value['name']);
                    $value['parent']    = is_bool($value['parent']) && !$value['parent'] ? false : $value['parent'];

                    $breadcrumbArr[]        = $value;
                }
            }
            else {
                $breadcrumbArr['id']          = $breadcrumb['id'];
                $breadcrumbArr['name']        = self::formatString($breadcrumb['name']);
                $breadcrumbArr['parent']      = is_bool($breadcrumb['parent']) && !$breadcrumb['parent'] ? false : $breadcrumb['parent'];
            }

        }

        return $breadcrumbArr;
    }
}