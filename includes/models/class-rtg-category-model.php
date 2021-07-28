<?php
/**
 * 2014-2019 Retargeting BIZ SRL
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@retargeting.biz so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Retargeting SRL <info@retargeting.biz>
 * @copyright 2014-2019 Retargeting SRL
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Class WooCommerceRTGCategoryModel
 */
class WooCommerceRTGCategoryModel extends \RetargetingSDK\Category
{
    /**
     * WooCommerceRTGCategoryModel constructor.
     * @param $category
     * @throws Exception
     */
    public function __construct($category = null)
    {
        if(!$category instanceof WP_Term)
        {
            if (is_product_category())
            {
                $category = get_queried_object();
            }
            elseif (!empty($category))
            {
                $category = get_term_by('id', $category, 'product_cat');
            }
        }

        if ($category instanceof WP_Term && !empty($category->term_id))
        {
            $this->_setCategoryData($category);
        }
    }

    /**
     * @param $category
     * @throws Exception
     */
    private function _setCategoryData($category)
    {
        $this->setId($category->term_id);
        $this->setName($category->name);
        $this->setUrl(get_term_link( $category ));

        if (!empty($category->parent))
        {
            $breadcrumbs = [];

            $ancestors = get_ancestors( $category->term_id, 'product_cat' );
            $ancestors = array_reverse( $ancestors );

            foreach ( $ancestors as $ancestor )
            {
                $ancestor = get_term( $ancestor, 'product_cat' );

                if ( !is_wp_error( $ancestor ) && $ancestor )
                {
                    $breadcrumbs[] = [
                        'id'     => $ancestor->term_id,
                        'name'   => $ancestor->name,
                        'parent' => !empty($ancestor->parent) ? $ancestor->parent : false
                    ];
                }
            }

            if (!empty($breadcrumbs))
            {
                $this->setParent($category->parent);
                $this->setBreadcrumb($breadcrumbs);
            }
        }
    }
}