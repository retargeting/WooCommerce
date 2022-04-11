<?php
/**
 * Created by PhpStorm.
 * User: bratucornel
 * Date: 2019-02-19
 * Time: 08:04
 */

namespace RetargetingSDK;

class Checkout extends AbstractRetargetingSDK
{
    protected $productIds = [];

    /**
     * @return array
     */
    public function getProductIds()
    {
        return $this->productIds;
    }

    /**
     * @param array $productIds
     */
    public function setProductIds(array $productIds)
    {
        $productIds = is_array($productIds) ? $productIds : (array)$productIds;

        $this->productIds = $productIds;
    }

    /**
     * @param bool $encoded
     * @return array|string
     */
    public function getData($encoded = true)
    {
        $checkout = $this->getProductIds();

        return $encoded ? $this->toJSON($checkout) : $checkout;
    }
}