<?php

namespace RetargetingSDK\Javascript\Item;

/**
 * Class Email
 * @package RetargetingSDK\Javascript\Item
 */
class Email extends AbstractItem
{
    /**
     * Email constructor.
     * @param $data
     */
    public function __construct($data)
    {
        if($this->isNotEmptyJSON($data))
        {
            $this->setParams('_ra.setEmailInfo = ' . $data . ';');
            $this->setMethod('_ra.setEmail(_ra.setEmailInfo);');
        }
    }
}