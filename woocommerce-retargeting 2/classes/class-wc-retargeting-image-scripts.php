<?php
/**
 * 
 * Image Scripts
 */

if (!defined('ABSPATH')) {
    exit;
}


 class WC_Retargeting_Image_Scripts {

     public function click_retaregeting_image($product) {
        global $product;
        $imageScript = '
            <script type="text/javascript">
                (function($) {
                    if (document.getElementsByClassName(".woocommerce-main-image") > 0 ) {
                        jQuery(".woocommerce-main-image").click(function() {
                            _ra.clickImage("' . $product->get_id() . '");
                        });
                    }

                    jQuery(".woocommerce-product-gallery__image").click(function() {
                        _ra.clickImage("' . $product->get_id() . '");
                    });
                })(jQuery);
            </script>
        ';
        return $imageScript;
     }
 }
?>