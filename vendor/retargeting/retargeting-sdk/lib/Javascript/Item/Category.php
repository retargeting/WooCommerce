<?php

namespace RetargetingSDK\Javascript\Item;

/**
 * Class Category
 * @package RetargetingSDK\Javascript\Item
 */
class Category extends AbstractItem
{
    /**
     * Category constructor.
     * @param $category
     */
    public function __construct($category)
    {
        if($this->isNotEmptyJSON($category))
        {
            $this->setParams('_ra.sendCategoryInfo = ' . $category . ';');
            $this->setMethod('_ra.sendCategory(_ra.sendCategoryInfo);');
        }
    }
}