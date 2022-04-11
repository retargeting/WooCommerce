<?php

namespace RetargetingSDK\Javascript\Item;

/**
 * Class PageNotFound
 * @package RetargetingSDK\Javascript\Item
 */
class PageNotFound extends AbstractItem
{
    /**
     * PageNotFound constructor.
     */
    public function __construct()
    {
        $this->setParams('_ra.pageNotFoundInfo = true;');
        $this->setMethod('_ra.pageNotFound();');

    }
}