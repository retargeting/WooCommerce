<?php

namespace RetargetingSDK\Javascript\Item;

/**
 * Class PageHome
 * @package RetargetingSDK\Javascript\Item
 */
class PageHome extends AbstractItem
{
    /**
     * PageHome constructor.
     */
    public function __construct()
    {
        $this->setParams('_ra.visitHomePageInfo = true;');
        $this->setMethod('_ra.visitHomePage();');
    }
}