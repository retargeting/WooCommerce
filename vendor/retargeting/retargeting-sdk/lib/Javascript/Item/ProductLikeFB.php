<?php

namespace RetargetingSDK\Javascript\Item;

/**
 * Class ProductLikeFB
 * @package RetargetingSDK\Javascript\Item
 */
class ProductLikeFB extends AbstractItem
{
    /**
     * ProductLikeFB constructor.
     * @param $productId
     */
    public function __construct($productId)
    {
        if(!empty($productId))
        {
            $jsCode  = 'window.fbAsyncInit = function() { ';
            $jsCode .= 'FB.Event.subscribe(\'edge.create\', function(response) { ';
            $jsCode .= 'if (_ra.ready !== undefined) { ';
            $jsCode .= '_ra.likeFacebook(' . $productId . ');';
            $jsCode .= '}';
            $jsCode .= '});';
            $jsCode .= '};';

            $this->setParams($jsCode);
        }
    }
}