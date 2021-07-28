<?php

namespace RetargetingSDK\Javascript\Item;

/**
 * Class Checkout
 * @package RetargetingSDK\Javascript\Item
 */
class Checkout extends AbstractItem
{
    /**
     * Checkout constructor.
     * @param string $ids
     */
    public function __construct($ids)
    {
        if($this->isNotEmptyJSON($ids))
        {
            $this->setParams('_ra.checkoutIdsInfo = ' . $ids . ';');
            $this->setMethod('_ra.checkoutIds(_ra.checkoutIdsInfo);');
        }
    }

}