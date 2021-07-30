<?php

namespace RetargetingSDK\Javascript\Item;

/**
 * Class Order
 * @package RetargetingSDK\Javascript\Item
 */
class Order extends AbstractItem
{
    /**
     * Order constructor.
     * @param $orderInfo
     * @param $orderProducts
     */
    public function __construct($orderInfo, $orderProducts)
    {
        if($this->isNotEmptyJSON($orderInfo))
        {
            $params  = '_ra.saveOrderInfo = ' . $orderInfo . ';';
            $params .= '_ra.saveOrderProducts = ' . $orderProducts . ';';

            $this->setParams($params);
            $this->setMethod('_ra.saveOrder(_ra.saveOrderInfo, _ra.saveOrderProducts);');
        }
    }
}