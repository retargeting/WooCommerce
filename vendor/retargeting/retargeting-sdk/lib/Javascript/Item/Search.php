<?php

namespace RetargetingSDK\Javascript\Item;

/**
 * Class Search
 * @package RetargetingSDK\Javascript\Item
 */
class Search extends AbstractItem
{
    /**
     * Search constructor.
     * @param $keywords
     */
    public function __construct($keywords)
    {
        if(is_string($keywords) || is_numeric($keywords) && !empty($keywords))
        {
            $this->setMethod('_ra.sendSearchTerm("' . $keywords . '");');
        }
    }
}