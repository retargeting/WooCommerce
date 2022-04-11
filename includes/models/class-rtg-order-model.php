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
 * Class WooCommerceRTGOrderModel
 */
class WooCommerceRTGOrderModel extends \RetargetingSDK\Order
{
    /**
     * WooCommerceRTGOrderModel constructor.
     * @param $orderId
     */
    public function __construct($orderId)
    {
        $this->_setOrderData($orderId);
    }

    /**
     * @param $orderId
     */
    private function _setOrderData($orderId)
    {
        $order = new WC_Order($orderId);

        if (!empty($order))
        {
            $this->setOrderNo($order->get_id());
            $this->setFirstName($order->get_billing_first_name());
            $this->setLastName($order->get_billing_last_name());
            $this->setEmail($order->get_billing_email());
            $this->setPhone($order->get_billing_phone());
            $this->setState($order->get_billing_state());
            $this->setCity($order->get_billing_city());
            $this->setAddress($order->get_billing_address_1() . ' ' . $order->get_billing_address_2());
            $this->setDiscount($order->get_discount_total());
            $this->setShipping($order->get_shipping_total());
            $this->setTotal($order->get_total());

            if ($order->get_used_coupons())
            {
                $coupons = [];

                foreach ($order->get_used_coupons() as $coupon)
                {
                    $coupons[] = $coupon;
                }

                $this->setDiscountCode(implode(', ', $coupons));
            }

            foreach ($order->get_items() AS $itemId => $itemData)
            {
                $orderProduct = $itemData->get_data();

                $this->setProduct(
                    $orderProduct['product_id'],
                    $orderProduct['quantity'],
                    round($orderProduct['total'] + (isset($orderProduct['subtotal_tax']) ? $orderProduct['subtotal_tax'] : 0)));

            }
        }
    }
}