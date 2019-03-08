<?php
/**
 * 
 * Image Scripts
 */

if (!defined('ABSPATH')) {
    exit;
}


 class WC_Retargeting_Image_Scripts {

    
      //Get product id when user clicks on an image
     

     public function click_retaregeting_image($product) {
        
        $imageScript = '<script type="text/javascript">
            var _ra = _ra || {};

            var mainImg = document.getElementsByClassName("woocommerce-main-image");
            if(mainImg.length > 0) {
                for(var i = 0; i < mainImg.length; i++) {
                    mainImg[i].onclick = function(e) {
                        _ra.clickImage("' . $product->get_id() . '");
                    }
                }
            }

            var img = document.getElementsByClassName("woocommerce-product-gallery__image");
                for(var i = 0; i < img.length; i++) {
                    img[i].onclick = function(e) {
                        _ra.clickImage("' . $product->get_id() . '");
                    }
                }
        </script>
    ';
        return $imageScript;
     }
 }
?>