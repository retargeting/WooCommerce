<?php

namespace RetargetingSDK\Javascript\Item;

/**
 * Class CartAdd
 * @package RetargetingSDK\Javascript\Item
 */
class CartAdd extends AbstractItem
{
    /**
     * CartAdd constructor.
     * @param $productId
     * @param $quantity
     * @param array $variation
     * @param $addToCartButtonId
     * @param $quantityInputId
     */
    public function __construct($productId, $quantity, array $variation, $addToCartButtonId, $quantityInputId)
    {
        $addToCart = [
            'product_id' => $productId,
            'quantity' => $quantity, // default 1
            'variation' => !empty($variation) ? $variation : false
        ];

        $addToCartSelector =
            $addToCartButtonId !== ""
                ?
                "document.getElementById('$addToCartButtonId')"
                :
                "document.getElementsByClassName('single_add_to_cart_button')[0]"
        ;

        $script =
            "if(document.querySelector(\"input[id ^= '$quantityInputId']\") !== null) {
                document.querySelector(\"input[id ^= '$quantityInputId']\").onchange = function() {
                _ra.addToCartInfo.quantity = this.value;
                }};
            if($addToCartSelector !== null) {
               $addToCartSelector.onclick = function() {
               _ra.addToCart(_ra.addToCartInfo.product_id, _ra.addToCartInfo.quantity, _ra.addToCartInfo.variation);
               }};
             ";


        $this->setParams('_ra.addToCartInfo = ' . json_encode($addToCart) . ';');
        $this->setMethod( $script );
    }
}