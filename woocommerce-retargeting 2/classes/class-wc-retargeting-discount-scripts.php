<?php
/**
 * Discount Scripts
 */

if (!defined('ABSPATH')) {
    exit;
}

 class WC_Retargeting_Discount_Scripts {
    
    public function discount_api_template($template){
        global $wp_query;

        if (isset($wp_query->query['retargeting']) && $wp_query->query['retargeting'] == 'discounts') {
            if (isset($wp_query->query['key']) && isset($wp_query->query['value']) && isset($wp_query->query['type']) && isset($wp_query->query['count'])) {
                if ($wp_query->query['key'] != "" && $wp_query->query['key'] == $this->token && $wp_query->query['value'] != "" && $wp_query->query['type'] != "" && $wp_query->query['count'] != "") {
                    // If everything is ok, generate and show the discount codes
                    echo generate_coupons($wp_query->query['count']);
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
     
    public function generate_coupons() {
        global $wp_query;
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

    public function woocommerce_verify_discount(){
        global $woocommerce;
        $o = new WC_Coupon($code);
        if ($o->exists == 1) {
            return false;
        } else {
            return true;
        }
    }

    public function woocommerce_add_discount(){
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

    public function retargeting_api_add_query_vars() {
        $vars[] = "retargeting";
        $vars[] = "key";
        $vars[] = "value";
        $vars[] = "type";
        $vars[] = "count";
        return $vars;
    }
 }
?>