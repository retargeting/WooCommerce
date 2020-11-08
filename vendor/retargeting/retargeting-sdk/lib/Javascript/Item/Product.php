<?php

namespace RetargetingSDK\Javascript\Item;

/**
 * Class Product
 * @package RetargetingSDK\Javascript\Item
 */
class Product extends AbstractItem
{
    /**
     * Product constructor.
     * @param $product
     */
    public function __construct($product)
    {
        if($this->isNotEmptyJSON($product))
        {
            $this->setParams('_ra.sendProductInfo = ' . $product . ';');
            $this->setMethod('_ra.sendProduct(_ra.sendProductInfo);');
        }
    }
}