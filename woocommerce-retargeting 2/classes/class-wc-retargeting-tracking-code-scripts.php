<?php
/**
 * Tracking code Scripts
 */

if (!defined('ABSPATH')) {
    exit;
}



class WC_Retargeting_Tracking_Scripts {

    public function get_retargeting_tracking_code($obj) {
        $trackingScript = '<!-- Retargeting Tracking Code '. WC_Retargeting_Tracking::VERSION .'-->
       <script type="text/javascript">
        (function(){
        ra_key = "' . esc_js($obj->domain_api_key) . '";
        ra_params = {
            add_to_cart_button_id: "' . esc_js($obj->add_to_cart_button_id) . '",
            price_label_id: "' . esc_js($obj->price_label_id) . '",
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