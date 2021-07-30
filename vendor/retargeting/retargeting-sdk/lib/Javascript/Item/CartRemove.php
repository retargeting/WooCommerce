<?php

namespace RetargetingSDK\Javascript\Item;

/**
 * Class CartRemove
 * @package RetargetingSDK\Javascript\Item
 */
class CartRemove extends AbstractItem
{
    /**
     * CartRemove constructor.
     * @param $productId
     * @param $quantity
     * @param array $variation
     */
    public function __construct($productId, $quantity, array $variation)
    {
        $removeFromCart = [
            'product_id' => $productId,
            'quantity'   => $quantity,
            'variation'  => !empty($variation) ? $variation : false
        ];

        $this->setParams('_ra.removeFromCartInfo = ' . json_encode($removeFromCart) . ';');
        $this->setMethod('_ra.removeFromCart(_ra.removeFromCartInfo.product_id, _ra.removeFromCartInfo.quantity, _ra.removeFromCartInfo.variation);');
    }

}