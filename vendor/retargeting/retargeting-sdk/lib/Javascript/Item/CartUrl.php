<?php

namespace RetargetingSDK\Javascript\Item;

/**
 * Class CartUrl
 * @package RetargetingSDK\Javascript\Item
 */
class CartUrl extends AbstractItem
{
    /**
     * CartUrl constructor.
     * @param $cartUrl
     */
    public function __construct($cartUrl)
    {
        if(!empty($cartUrl))
        {
            $params = [
                'url' => $cartUrl
            ];

            $params = '_ra.setCartUrlInfo = ' . json_encode($params) . ';';
            $method = '_ra.setCartUrl(_ra.setCartUrlInfo.url);';

            $this->setParams($params);
            $this->setMethod($method);
        }
    }
}