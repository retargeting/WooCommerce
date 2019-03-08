<?php
/**
 *  Cart Scripts
 */

if (!defined('ABSPATH')) {
    exit;
}

class WC_Retargeting_Cart_Scripts{

    /**
     *   Get data stored from the object removed and 
     *   add it as params to removeFromCart method
     */

    public function remove_from_retargeting_cart() 
    {
        $removeScript =  '<script type="text/javascript">
                    var _ra = _ra || {};
                    var removeBtn = document.getElementsByClassName("remove");

                    for(var i = 0; i < removeBtn.length;i++) {
                        removeBtn[i].onclick = function() {
                            var productId = this.getAttribute("data-product_id");
                            var productQuantity = this.parentElement.querySelectorAll(\'.qty\').value ? this.parentElement.querySelectorAll(\'.qty\' ).value : \'1\';
                            alert(productId + " - " + productQuantity);
                            _ra.removeFromCart(productId, productQuantity, false, function() {
                                console.log("Product removed from cart");
                            });
                        }
                    }
            </script>';
    return $removeScript;
    }

    /**
     *  Get product id and use it in addToCart method
     */

    public function add_to_retargeting_cart($product)
    {
        $addScript = '<script type="text/javascript">
        var _ra = _ra || {};
        var addBtn = document.getElementsByClassName("single_add_to_cart_button");

        for(var i = 0; i < addBtn.length;i++) {
            addBtn[i].onclick = function(e) {
                _ra.addToCart("' . $product->get_id() . '",1,false,function(){console.log("cart")});
            }
        }

        </script>';
            return $addScript;
    }
    
}


?>