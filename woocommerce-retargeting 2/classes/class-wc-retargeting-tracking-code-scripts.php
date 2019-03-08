<?php
/**
 * Tracking code Scripts
 */

if (!defined('ABSPATH')) {
    exit;
}



class WC_Retargeting_Tracking_Scripts {

    /**
     *  Retargeting tracking code
     */

    public function get_retargeting_tracking_code($object)
    {
        $trackingScript = '<!-- Retargeting Tracking Code '. WC_Retargeting_Tracking::VERSION .'-->
       <script type="text/javascript">
        (function(){
        ra_key = "' . esc_js($object->domain_api_key) . '";
        ra_params = {
            add_to_cart_button_id: "' . esc_js($object->add_to_cart_button_id) . '",
            price_label_id: "' . esc_js($object->price_label_id) . '",
        };
        var ra = document.createElement("script"); ra.type ="text/javascript"; ra.async = true; ra.src = ("https:" ==
        document.location.protocol ? "https://" : "http://") + "tracking.retargeting.biz/v3/rajs/" + ra_key + ".js";
        var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ra,s);})();
        </script>
        <!-- Retargeting Tracking Code -->';
        return $trackingScript;
    }
}

?>