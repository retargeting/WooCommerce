<?php
/**
 * Product Scripts
 */
if (!defined('ABSPATH')) {
    exit;
}
 class WC_Retargeting_Product_Scripts {
    
    
    public function get_retargeting_product_categories($product) {
        $categories = get_the_terms($product->get_id(), 'product_cat');
        $cat = array();
        if ($categories) {
            foreach ($categories as $category) {
                $cat['catid'] = $category->term_id;
                $cat['cat'] = $category->name;
                $cat['catparent'] = $category->parent;
            }
        } else {
            $cat['catid'] = 1;
            $cat['cat'] = "Root";
            $cat['catparent'] = "false";
        }
        return $cat;
    }
    public function send_category($query) {
        if (is_product_category()) {
            global $$query;
            $categories = $query->get_queried_object();
            if ($categories) {
                $categoryScript = '<script type="text/javascript">
                var _ra = _ra || {};
                _ra.sendCategoryInfo = {
                    "id": ' . $categories->term_id . ',
                    "name" : "' . htmlspecialchars($categories->name) . '",
                    "parent": false,
                    "breadcrumb": []
                }

                if (_ra.ready !== undefined) {
                    _ra.sendCategory(_ra.sendCategoryInfo);
                }

                </script>';
            } else {
                $categoryScript = '<script type="text/javascript">
                var _ra = _ra || {};
                _ra.sendCategoryInfo = {
                    "id": 1,
                    "name" : Root,
                    "parent": false,
                    "breadcrumb": []
                }

                if (_ra.ready !== undefined) {
                    _ra.sendCategory(_ra.sendCategoryInfo);
                }

                </script>';
            }
            return $categoryScript;
        }
    }

    public function send_retargeting_product($product, $image_url, $price, $specialPrice, $stock, $cat){
                $scriptProduct = '
                <script>
                    var _ra = _ra || {};
                    _ra.sendProductInfo = {
                        "id": ' . $product->get_id() . ',
                        "name": "' . htmlspecialchars($product->get_title()) . '",
                        "url": "' . get_permalink() . '",
                        "img": "' . $image_url . '",
                        "price": "' . $price . '",
                        "promo": "' . $specialPrice . '",
                        "inventory": {
                            "variations": false,
                            "stock": ' . $stock . ',
                        },
                        "brand": false,
                        "category": [
                                {
                                    "id": ' . $cat['catid'] . ',
                                    "name": "' . $cat['cat'] . '",
                                    "parent": false,
                                    "breadcrumb": []
                                }
                        ]
                    };
                    if (_ra.ready !== undefined) {
                        _ra.sendProduct(_ra.sendProductInfo);
                    }
                    
    //Set Variation
          (function($) {

            var _ra_sv = document.querySelectorAll("[data-attribute_name]");
            if (_ra_sv.length > 0) {
                for (var i = 0; i < _ra_sv.length; i++) {
                    _ra_sv[i].addEventListener("change", function() {
                                var _ra_vcode = [];
                                var _ra_vdetails = {};
                                var _ra_v = document.querySelectorAll("[data-attribute_name]");
                                for (var i = 0; i < _ra_v.length; i++) {
                                    var _ra_label = document.querySelector(\'[for="\' + _ra_v[i].getAttribute(\'id\') + \'"\');
                                        _ra_label = (_ra_label !== null ? _ra_label = document.querySelector(\'[for="\' + _ra_v[i].getAttribute(\'id\') + \'"\').textContent : _ra_v[i].getAttribute(\'data-option\') );
                                                var _ra_value = (typeof _ra_v[i].value !== \'undefined\' && _ra_v[i].value !== "" ? _ra_v[i].value : "Default");
                                                    _ra_value = _ra_value.replace(/-/g, "_"); 
                                                    _ra_vcode.push(_ra_value);
                                                    _ra_vdetails[_ra_value] = {
                                                        "category_name": _ra_label,
                                                        "category": _ra_label,
                                                        "value":  _ra_value
                                                    };
                                                }
                                                _ra.setVariation(' . $product->get_id() . ', {
                                                        "code": _ra_vcode.join(\'-\'),
                                                        "stock": 1,
                                                        "details": _ra_vdetails
                                                        });
                                                });
                                        }
                                    }                    
          })(jQuery);

                    </script>';
                    return $scriptProduct;
    }

    public function getPricesForVariableProduct() {
        $prices = $product->get_variation_prices();
        $min_price = current($prices['sale_price']);
        $max_price = end($prices['regular_price']);
        $price = $min_price !== $max_price ? $max_price : $min_price;
        $specialPrice = 0;

        if ($product->is_on_sale()) {
            $specialPrice = $product->get_sale_price();
        }

        $price = wc_get_price_including_tax($product, array('price' => $price));
        $specialPrice = wc_get_price_including_tax($product, array('price' => $specialPrice));
        
        return array(
            (!empty($price) ? $price : 0),
            (!empty($specialPrice) ? $specialPrice : 0)
            );
    }

    public function getPricesForGroupedProducts() {
        $getPrice = $product->get_price();
        $price = (!empty($getPrice) ? $getPrice : 0);
        $getSpecialPrice = $product->get_sale_price();
        $specialPrice = (!empty($getSpecialPrice) ? $getSpecialPrice : 0);
        $price = wc_get_price_including_tax( $product, array('price' => $price) );
        $specialPrice = wc_get_price_including_tax( $product, array('price' => $specialPrice) );
        
        return array(
            (!empty($price) ? $price : 0),
            (!empty($specialPrice) ? $specialPrice : 0)
            );
    }

    public function discount_retargeting_api_template($template, $wp_query) {
        if (isset($wp_query->query['retargeting']) && $wp_query->query['retargeting'] == 'discounts') {
            if (isset($wp_query->query['key']) && isset($wp_query->query['value']) && isset($wp_query->query['type']) && isset($wp_query->query['count'])) {
                if ($wp_query->query['key'] != "" && $wp_query->query['key'] == $this->token && $wp_query->query['value'] != "" && $wp_query->query['type'] != "" && $wp_query->query['count'] != "") {
                    // If everything is ok, generate and show the discount codes
                    echo $this->generate_retargeting_coupons($wp_query->query['count']);
                    exit;
                } else {
                    echo json_encode(array("status" => false, "error" => "0002: Invalid Parameters!"));
                    exit;
                }
            } else {
                echo json_encode(array("status" => false, "error" => "0001: Missing Parameters!"));
                exit;
            }
        }
    }
    public function generate_retargeting_coupons($query) {
        $couponChars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $couponCodes = array();
        for ($x = 0; $x < $count; $x++) {
            $couponCode = "";
            for ($i = 0; $i < 8; $i++) {
                $couponCode .= $couponChars[mt_rand(0, strlen($couponChars) - 1)];
            }
            if ($this->woocommerce_verify_discount($couponCode)) {
                $this->woocommerce_add_discount($couponCode, $wp_query->query['value'], $wp_query->query['type']);
                $couponCodes[] = $couponCode;
            } else {
                $x -= 1;
            }
        }
        return json_encode($couponCodes);
    }

    public function woocommerce_verify_discount($code) {
        global $woocommerce;
        $o = new WC_Coupon($code);
        if ($o->exists == 1) {
            return false;
        } else {

            return true;
        }
    }

    public function woocommerce_add_discount($code, $discount, $type)
    {
        global $wp_query;

        //Retargeting discount Types
        /*
        0 - fixed value,
        1 - percentage value,
        2 - free delivery
        */

        $type = $wp_query->query['type'];

        if ($type == 0) {
            $discount_type = 'fixed_cart';
        } elseif ($type == 1) {
            $discount_type = 'percent';
        } elseif ($type == 2) {
            $discount_type = '';
        }
        $coupon_code = $code; // Code
        $amount = $discount; // Amount
        // $discount_type = 'fixed_cart'; // Type: fixed_cart, percent, fixed_product, percent_product

        $coupon = array(
            'post_title' => $coupon_code,
            'post_content' => '',
            'post_status' => 'future',
            'post_author' => 1,
            'post_type' => 'shop_coupon'
        );

        $new_coupon_id = wp_insert_post($coupon);

        // Add meta
        update_post_meta($new_coupon_id, 'discount_type', $discount_type);
        update_post_meta($new_coupon_id, 'coupon_amount', $amount);
        update_post_meta($new_coupon_id, 'individual_use', 'no');
        update_post_meta($new_coupon_id, 'product_ids', '');
        update_post_meta($new_coupon_id, 'exclude_product_ids', '');
        update_post_meta($new_coupon_id, 'usage_limit', '');
        update_post_meta($new_coupon_id, 'expiry_date', '');
        update_post_meta($new_coupon_id, 'apply_before_tax', 'yes');
        update_post_meta($new_coupon_id, 'free_shipping', 'no');


    }
 }
?>