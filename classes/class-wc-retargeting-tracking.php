<?php
/**
 * Exit if accessed directly
 **/
​
if (!defined('ABSPATH')) {
    exit;
}
​
require_once (dirname(__FILE__).'/../lib/Retargeting_REST_API_Client.php');
​
class WC_Integration_Retargeting_Tracking extends WC_Integration
{
    protected static $product_type = array(
        'simple',
        'variable',
        'grouped'
    );
​
    /*
    * Construct
    */
    public function __construct()
    {
        $this->id = 'retargeting';
        $this->method_title = "Retargeting";
        $this->method_description = __('Retargeting and marketing automation for ecommerce.');
​
        $this->init_form_fields();
        $this->init_settings();
​
        $this->domain_api_key = $this->get_option('domain_api_key');
        $this->token = $this->get_option('token');
        $this->help_pages = $this->get_option('help_pages');
​
        add_action('woocommerce_update_options_integration_retargeting', array($this, 'process_admin_options'));
        if($this->domain_api_key && $this->domain_api_key != ''){
            //Retargeting Tracking Code V3
           add_action('wp_head', array($this, 'get_retargeting_tracking_code'), 999);
        } else {
            //Retargeting Tracking Code V2
            add_action('wp_head', array($this, 'get_retargeting_tracking_code_v2'), 999);
        }
​
        add_action('wp_head', array($this, 'set_email'), 9999);
​
        //Cat
        add_action('woocommerce_before_main_content', array($this, 'send_category'), 30, 0);
        //Prod
        add_action('woocommerce_before_single_product', array($this, 'send_product'), 20, 0);
        //add2cart
        add_action('woocommerce_after_add_to_cart_button', array($this, 'add_to_cart'));
        //click_image
        add_action('woocommerce_before_single_product', array($this, 'click_image'), 30, 0);
        //Mouse Over Price
//        add_action('woocommerce_before_single_product', array($this, 'mouse_over_price'), 40, 0);
        //Mouse over add to cart
//        add_action('woocommerce_before_single_product', array($this, 'mouse_over_add_to_cart'), 50, 0);
        //Like Facebook
        add_action('woocommerce_before_single_product', array($this, 'like_facebook'), 50, 0);
        //HelpPages
        add_action('wp_footer', array($this, 'help_pages'), 999, 0);
        //CheckoutIds
        add_action('woocommerce_after_cart', array($this, 'checkout_ids'), 90, 0);
        add_action('woocommerce_after_checkout_form', array($this, 'checkout_ids'), 90, 0);
        // SaveOrder
        add_action('woocommerce_thankyou', array($this, 'save_order'));
        // API's
        add_action('template_redirect', array($this,'discount_api_template'));
        add_filter( 'query_vars', array($this,'retargeting_api_add_query_vars'));
​
​
    }
​
    /*
    * Init admin form
    */
    function init_form_fields()
    {
        //List all pages
        $allpages = get_pages();
        $pages = array();
        foreach ($allpages as $key => $page) {
            $pages[$page->post_name] = $page->post_title;
        }
​
        $this->form_fields = array(
            'domain_api_key' => array(
                'title' => __('Domain API KEY'),
                'description' => __('Insert retargeting Domain API Key. <a href="https://retargeting.biz/admin?action=api_redirect&token=5ac66ac466f3e1ec5e6fe5a040356997" target="_blank">Click here</a> to get your Domain API Key'),
                'type' => 'text',
                'default' => '',
            ),
            'token' => array(
                'title' => __('Token'),
                'description' => __('Insert Retargeting Token. <a href="https://retargeting.biz/admin?action=api_redirect&token=028e36488ab8dd68eaac58e07ef8f9bf" target="_blank">Click here</a> to get your Token'),
                'type' => 'text',
                'default' => '',
            ),
            'help_pages' => array(
                'title' => __('Help Pages'),
                'description' => __('Select All Help Pages (e.g. How to order?, FAQ, How I get the products?)'),
                'type' => 'multiselect',
                'options' => $pages
            ),
        );
    }
​
    /*
    * Retargeting Tracking Code V3
    */
    public function get_retargeting_tracking_code()
    {
        echo '<!-- Retargeting Tracking Code -->
       <script type="text/javascript">
        (function(){
        ra_key = "' . esc_js($this->domain_api_key) . '";
        ra_params = {
        add_to_cart_button_id: "add_to_cart_button_id",
        price_label_id: "price_label_id",
        };
        var ra = document.createElement("script"); ra.type ="text/javascript"; ra.async = true; ra.src = ("https:" ==
        document.location.protocol ? "https://" : "http://") + "tracking.retargeting.biz/v3/rajs/" + ra_key + ".plain.js";
        var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ra,s);})();
        </script>
        <script>
        _ra = _ra || {};
           _ra.setDev = {
               mode: 1
           };
​
           if( _ra.ready !== undefined ){
                _ra.setDev(1);
            }
        </script>
        <!-- Retargeting Tracking Code -->';
    }
​
    /*
     * Retargeting Tracking Code V2
     * */
    public function get_retargeting_tracking_code_v2()
    {
        echo '<!-- Retargeting Tracking Code -->
        <script type="text/javascript">
    (function(){
    var ra = document.createElement("script"); ra.type ="text/javascript"; ra.async = true; ra.src = ("https:" ==
    document.location.protocol ? "https://" : "http://") + "retargeting-data.eu/" +
    document.location.hostname.replace("www.","") + "/ra.js"; var s =
    document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ra,s);})();
    </script>
        <!-- Retargeting Tracking Code -->';
    }
​
    /*
    * SetEmail
    */
    public function set_email()
    {
        global $woocommerce;
        $email = array();
        $email['email'] = wp_get_current_user()->user_email;
        if ((!isset($_SESSION['set_email']) || $_SESSION['set_email'] != $email['email']) && (!empty($email['email']))) {
            echo '
            <script>
                var _ra = _ra || {};
                _ra.setEmailInfo = {
                "email": "' . $email['email'] . '"
                };
                if(_ra.ready !== undefined) {
                _ra.setEmail(_ra.setEmailInfo)
                }
            </script>';
            $_SESSION['set_email'] = $email['email'];
        }
    }
​
    /*
    * SendCategory
    */
    public function send_category()
    {
        if (is_product_category()) {
            global $wp_query;
            $categories = $wp_query->get_queried_object();
            if ($categories) {
                echo '<script>
                var _ra = _ra || {};
                _ra.sendCategoryInfo = {
                    "id": ' . $categories->term_id . ',
                    "name" : "' . $categories->name . '",
                    "parent": false,
                    "category_breadcrumb": []
                }
​
                if (_ra.ready !== undefined) {
                    _ra.sendCategory(_ra.sendCategoryInfo);
                }
​
                </script>';
            }
        }
    }
​
    /*
    * SendBrand
    */
    public function send_brand()
    {
​
    }
​
​
    /*
     * SendProduct
     * */
    public function send_product()
    {
        if (is_product()) {
            global $product;
​
            $variation_id = get_post_meta($this->id, '_min_regular_price_variation_id', true);
​
​
            if ($product instanceof WC_Product && $product->is_type(self::$product_type)) {
                if (!$variation_id) {
                    $price = $price = get_post_meta(get_the_ID(), '_min_variation_price', true);;
                } else {
                    $price = get_post_meta($variation_id, '_regular_price', true);
                }
                if ($price == '') {
                    $price = $product->get_regular_price();
                }
                $image_url = wp_get_attachment_url(get_post_thumbnail_id());
                $categories = get_the_terms($product->id, 'product_cat');
                $cat = array();
                if ($categories) {
                    foreach ($categories as $category) {
                        $cat['catid'] = $category->term_id;
                        $cat['cat'] = $category->name;
                        $cat['catparent'] = $category->parent;
                    }
                }
                $dsp = $product->get_sale_price();
                if (empty($dsp)) {
                    $sp = 0;
                } else {
                    $sp = $product->get_sale_price();
                }
                $stock = $product->is_in_stock() ? 1 : 0;
                echo '
                <script>
                    var _ra = _ra || {};
                    _ra.sendProductInfo = {
                        "id": ' . $product->id . ',
                        "name": "' . $product->get_title() . '",
                        "url": "' . get_permalink() . '",
                        "img": "' . $image_url . '",
                        "price": ' . $price . ',
                        "promo": ' . $sp . ',
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
                        			breadcrumb: []
                        		}
                        ]
                    };
//Set Variation
jQuery(document).ready(function(){
​
​
var _ra_sv = document.querySelectorAll("[data-attribute_name]");
if (_ra_sv.length > 0) {
for(var i = 0; i < _ra_sv.length; i ++) {
_ra_sv[i].addEventListener("change", function() {
var _ra_vcode = [], _ra_vdetails = {};
var _ra_v = document.querySelectorAll("[data-attribute_name]");
for(var i = 0; i < _ra_v.length; i ++) {
var _ra_label = document.querySelector(\'[for="\' + _ra_v[i].getAttribute(\'id\') + \'"\');
_ra_label = (_ra_label !== null ? _ra_label = document.querySelector(\'[for="\' + _ra_v[i].getAttribute(\'id\') + \'"\').textContent : _ra_v[i].getAttribute(\'data-option\') );
var _ra_value = (typeof _ra_v[i].value !== \'undefined\' ? _ra_v[i].value : _ra_v[i].textContent);
_ra_value = _ra_value.replace(/-/g, "_");
_ra_vcode.push(_ra_value);
_ra_vdetails[_ra_value] = {
"category_name": _ra_label,
"category": _ra_label,
"value": _ra_value,
"stock": ' . $stock . '
};
}
_ra.setVariation(' . $product->id . ', {
"code": _ra_vcode.join(\'-\'),
"details": _ra_vdetails
});
});
}
}
});
//set Variation
                    if (_ra.ready !== undefined) {
                        _ra.sendProduct(_ra.sendProductInfo);
​
                    }
                    </script>';
​
            }
        }
    }
​
    /*
    * AddToCart
    */
    public function add_to_cart()
    {
        if (is_product()) {
            global $product;
            echo '
                <script>
                jQuery(document).ready(function(){
                    jQuery(".single_add_to_cart_button").click(function(){
                        _ra.addToCart("' . $product->id . '",1,false,function(){console.log("cart")});
                    });
                });
                </script>';
        }
...