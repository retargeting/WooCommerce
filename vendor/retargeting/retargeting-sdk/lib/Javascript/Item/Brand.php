<?php

namespace RetargetingSDK\Javascript\Item;

/**
 * Class Brand
 * @package RetargetingSDK\Javascript\Item
 */
class Brand extends AbstractItem
{
    /**
     * Brand constructor.
     * @param $brand
     */
    public function __construct($brand)
    {
        if($this->isNotEmptyJSON($brand))
        {
            $this->setParams('_ra.sendBrandInfo = ' . $brand . ';');
            $this->setMethod('_ra.sendBrand(_ra.sendBrandInfo);');
        }
    }
}