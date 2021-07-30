<?php

namespace RetargetingSDK\Javascript\Item;

/**
 * Class PageHelp
 * @package RetargetingSDK\Javascript\Item
 */
class PageHelp extends AbstractItem
{
    /**
     * PageHelp constructor.
     */
    public function __construct()
    {
        $this->setParams('_ra.visitHelpPageInfo = true;');
        $this->setMethod('_ra.visitHelpPage();');
    }

}