<?php
/**
 * Media Scripts
 */

if (!defined('ABSPATH')) {
    exit;
}

 class WC_Retargeting_Media_Scripts {

    
    // Get product id for facebook
    
    public function like_retargeting_facebook($product) {
        $facebookScript = "<script type='text/javascript'>
            if (typeof FB != 'undefined') {
                FB.Event.subscribe('edge.create', function () {
                    _ra.likeFacebook(" . $product->get_id() . ");
                });
            };
        </script>";
        return $facebookScript;
    }
 }
?>