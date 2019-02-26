<?php
/**
 *  Cart Scripts
 */

if (!defined('ABSPATH')) {
    exit;
}
   
class WC_Retargeting_Cart_Scripts{
    
    public function remove_from_retargeting_cart() 
    {
        $removeScript =  '<script type="text/javascript">
                  (function($) {
                    $(".remove").click(function() {
                      var productId = $(this).data(\'product_id\');
                      var productQuantity = $(this).parent().parent().find( \'.qty\' ).val() ? $(this).parent().parent().find( \'.qty\' ).val() : \'1\';
                        _ra.removeFromCart(productId, productQuantity, false, function() {
                            console.log("Product removed from cart");
                        });
                    });                    
                  })(jQuery);
            </script>';
            return $removeScript;
    }

    public function add_to_retargeting_cart($product)
    {
        $addScript = '
            <script type="text/javascript">
            (function($) {
                $(".single_add_to_cart_button").click(function(){
                    _ra.addToCart("' . $product->get_id() . '",1,false,function(){console.log("cart")});
                });
            })(jQuery);
            </script>';
            return $addScript;
    }
    
}


?>