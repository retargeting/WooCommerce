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
 * Class WooCommerceRTGCheckoutModel
 */
class WooCommerceRTGCheckoutModel extends \RetargetingSDK\Checkout
{
    /**
     * WooCommerceRTGCheckoutModel constructor.
     */
    public function __construct()
    {
        $this->_setCheckoutProductIds();
    }

    /**
     * Set checkout product ids
     */
    private function _setCheckoutProductIds()
    {
        $productIds = [];

        foreach (WC()->cart->get_cart() AS $item => $value)
        {
            $productIds[] = $value['data']->get_id();
        }

        if (!empty($productIds))
        {
            $this->setProductIds($productIds);
        }
    }
}