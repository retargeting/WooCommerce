<?php
/**
 * Exit if accessed directly
 **/

if (!defined('ABSPATH')) {
    exit;
}

require_once(dirname(__FILE__) . '/../lib/Retargeting_REST_API_Client.php');

class WC_Integration_Retargeting_Tracking extends WC_Integration
{
    protected static $product_type = array(
        'simple',
        'variable',
        'grouped'
    );

    /*
    * Construct
    */
    public function __construct()
    {
        $this->id = 'retargeting';
        $this->method_title = "Retargeting";
        $this->method_description = __('Retargeting is a marketing automation tool that boosts the conversion rate and sales of your online store.');

        $this->init_form_fields();
        $this->init_settings();

        $this->domain_api_key = $this->get_option('domain_api_key');
        $this->token = $this->get_option('token');
        $this->add_to_cart_button_id = $this->get_option('add_to_cart_button_id');
        $this->price_label_id = $this->get_option('price_label_id');
        $this->help_pages = $this->get_option('help_pages');
        
        add_action('init', array($this, 'ra_session_init'));

        add_action('woocommerce_update_options_integration_retargeting', array($this, 'process_admin_options'));

        add_action('wp_head', array($this, 'get_retargeting_tracking_code'), 999);

        add_action('wp_head', array($this, 'set_email'), 9999);


        add_action('woocommerce_before_main_content', array($this, 'send_category'), 30, 0);

        add_action('woocommerce_before_single_product', array($this, 'send_product'), 20, 0);

        add_action('woocommerce_after_add_to_cart_button', array($this, 'add_to_cart'));
        
        add_action( 'woocommerce_after_cart', array($this, 'remove_from_cart' ));
        add_action( 'woocommerce_after_mini_cart', array($this, 'remove_from_cart' ));

        add_action('woocommerce_before_single_product', array($this, 'click_image'), 30, 0);

        add_action('woocommerce_before_single_product', array($this, 'like_facebook'), 50, 0);

        add_action('wp_footer', array($this, 'help_pages'), 999, 0);

        add_action('woocommerce_after_cart', array($this, 'checkout_ids'), 90, 0);
        add_action('woocommerce_after_checkout_form', array($this, 'checkout_ids'), 90, 0);

        add_action('woocommerce_thankyou', array($this, 'save_order'));

        add_action('template_redirect', array($this, 'discount_api_template'));
        add_filter('query_vars', array($this, 'retargeting_api_add_query_vars'));

    }

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

        $this->form_fields = array(
            'domain_api_key' => array(
                'title' => __('Tracking API KEY'),
                'description' => __('Insert Retargeting TRACKING API Key. <a href="https://retargeting.biz/admin?action=api_redirect&token=5ac66ac466f3e1ec5e6fe5a040356997" target="_blank" rel="noopener noreferrer">Click here</a> to get your Tracking API Key'),
                'type' => 'text',
                'default' => '',
            ),
            'token' => array(
                'title' => __('REST API Key'),
                'description' => __('Insert Retargeting REST API Key. <a href="https://retargeting.biz/admin?action=api_redirect&token=5ac66ac466f3e1ec5e6fe5a040356997" target="_blank" rel="noopener noreferrer">Click here</a> to get your Rest API Key'),
                'type' => 'text',
                'default' => '',
            ),
            'add_to_cart_button_id' => array(
              'title' => __('Add To Cart Button'),
              'description' => __('[Optional] CSS query selector for the button used to add a product to cart.'),
              'type' => 'text',
              'default' => '.entry-summary .single_add_to_cart_button'
            ),
            'price_label_id' => array(
              'title' => __('Price Label'),
              'description' => __('[Optional] CSS query selector for the main product price on a product page.'),
              'type' => 'text',
              'default' => '.entry-summary .woocommerce-Price-amount'
            ),
            'help_pages' => array(
                'title' => __('Help Pages'),
                'description' => __('Select All Help Pages (e.g. How to order?, FAQ, How I get the products?)'),
                'type' => 'multiselect',
                'options' => $pages
            ),
        );
    }
    
    /*
    *   Initialize WP session
    */
    function ra_session_init() 
    {
        if ( !session_id() ) {
            session_start();
        }
        
    }
    
    /*
    * Retargeting Tracking Code V3
    */
    public function get_retargeting_tracking_code()
    {
        echo '<!-- Retargeting Tracking Code '. WC_Retargeting_Tracking::VERSION .'-->
       <script type="text/javascript">
        (function(){
        ra_key = "' . esc_js($this->domain_api_key) . '";
        ra_params = {
        add_to_cart_button_id: "' . esc_js($this->add_to_cart_button_id) . '",
        price_label_id: "' . esc_js($this->price_label_id) . '",
        };
        var ra = document.createElement("script"); ra.type ="text/javascript"; ra.async = true; ra.src = ("https:" ==
        document.location.protocol ? "https://" : "http://") + "tracking.retargeting.biz/v3/rajs/" + ra_key + ".js";
        var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ra,s);})();
        </script>
        <!-- Retargeting Tracking Code -->';
    }

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
                    "name" : "' . htmlspecialchars($categories->name) . '",
                    "parent": false,
                    "breadcrumb": []
                }

                if (_ra.ready !== undefined) {
                    _ra.sendCategory(_ra.sendCategoryInfo);
                }

                </script>';
            } else {
                echo '<script>
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
        }
    }

    /*
     * SendProduct
     * */
    public function send_product()
    {
        if (is_product()) {
            global $product;

            $variation_id = get_post_meta($this->id, '_min_regular_price_variation_id', true);

            if ($product instanceof WC_Product && $product->is_type(self::$product_type)) {


                // Prices

                // Simple product type

                switch ($product->get_type()) {
                    case 'variable':
                        list($price, $specialPrice) = $this->getPricesForVariableProducts($product);
                        break;
                    case 'grouped':
                        list($price, $specialPrice) = $this->getPricesForGroupedProducts($product);
                        break;
                    default:
                        $price = wc_get_price_including_tax( $product, array('price' => $product->get_regular_price() ) );
                        $salePrice = wc_get_price_including_tax( $product, array('price' => $product->get_price() ) );
                        $salePrice = $price == $salePrice ? 0 : $salePrice;
                        $specialPrice = (!empty($salePrice) ? $salePrice : 0);
                        break;
                }

                $image_url = wp_get_attachment_url(get_post_thumbnail_id());
                if (empty($image_url)) {
                    $image_url = site_url() . '/wp-content/plugins/woocommerce/assets/images/placeholder.png';
                }

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

                $stock = $product->is_in_stock() ? 1 : 0;
                echo '
                <script>
                    var _ra = _ra || {};
                    _ra.sendProductInfo = {
                        "id": ' . $product->get_id() . ',
                        "name": "' . htmlspecialchars($product->get_title()) . '",
                        "url": "' . get_permalink() . '",
                        "img": "' . $image_url . '",
                        "price": ' . $price . ',
                        "promo": ' . $specialPrice . ',
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

            }
        }
    }

    /*
    * AddToCart
    */
    public function add_to_cart()
    {
        if (is_product()) {
            global $product;
            echo '
                <script>
                (function($) {
                    $(".single_add_to_cart_button").click(function(){
                        _ra.addToCart("' . $product->get_id() . '",1,false,function(){console.log("cart")});
                    });
                })(jQuery);
                </script>';
        }
    }
    
    /*
    * removeFromCart
    */
    public function remove_from_cart()
    {
        echo '<script>
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
    }
    
    /*
    * ClickImage
    */
    public function click_image()
    {
        global $product;
        echo '
            <script>
                (function($) {
                    jQuery(".woocommerce-main-image").click(function() {
                        _ra.clickImage("' . $product->get_id() . '");
                    });
                })(jQuery);
            </script>
        ';
    }

    /*
    * LikeFacebook
    */
    public function like_facebook()
    {
        global $product;
        echo "<script>
            if (typeof FB != 'undefined') {
                FB.Event.subscribe('edge.create', function () {
                    _ra.likeFacebook(" . $product->get_id() . ");
                });
            };
        </script>";
    }

    /*
    * SaveOrder
    */
    public function save_order($order_id)
    {
        if (is_numeric($order_id) && $order_id > 0) {
            $order = new WC_Order($order_id);
            $coupons_list = '';
            if ($order->get_used_coupons()) {
                $coupons_count = count($order->get_used_coupons());
                $i = 1;
                foreach ($order->get_used_coupons() as $coupon) {
                    $coupons_list .= $coupon;
                    if ($i < $coupons_count) {
                        $coupons_list .= ', ';
                        $i++;
                    }
                }
            }

            $data = array(
                'line_items' => array(),
            );

            foreach ((array)$order->get_items() as $item_id => $item) {
                $_product = apply_filters('woocommerce_order_item_product', $order->get_product_from_item($item), $item);
                $item_meta = new WC_Order_Item_Meta($item['item_meta'], $_product);
                if (apply_filters('woocommerce_order_item_visible', true, $item)) {
                    $line_item = array(
                        'id' => $item['product_id'],
                        'name' => $item['name'],
                        'price' => $item['line_subtotal'],
                        'quantity' => $item['qty'],
                        'variation_code' => ($item['variation_id'] == 0) ? "" : $item['variation_id']
                    );
                }
                $data['line_items'][] = $line_item;
            }
            

            echo '<script>
                var _ra = _ra || {};
                _ra.saveOrderInfo = {
                    "order_no": ' . $order->get_id() . ',
                    "lastname": "' . $order->get_billing_last_name() . '",
                    "firstname": "' . $order->get_billing_first_name() . '",
                    "email": "' . $order->get_billing_email() . '",
                    "phone": "' . $order->get_billing_phone() . '",
                    "state": "' . $order->get_billing_state() . '",
                    "city": "' . $order->get_billing_city() . '",
                    "address": "' . $order->get_billing_address_1() . " " . $order->get_billing_address_2() . '",
                    "discount_code": "' . $coupons_list . '",
                    "discount": ' . (empty($order->get_discount) ? 0 : $order->get_discount) . ',
                    "shipping": ' . (empty($order->get_total_shipping) ? 0 : $order->get_total_shipping) . ',
                    "rebates": 0,
                    "fees": 0,
                    "total": ' . $order->get_total() . '
                };
                _ra.saveOrderProducts =
                    ' . json_encode($data['line_items']) . '
                ;
                
                if( _ra.ready !== undefined ){
                    _ra.saveOrder(_ra.saveOrderInfo, _ra.saveOrderProducts);
                }
            </script>';
        }

        //REST API

        $orderInfo = array(
            "order_no" => $order->get_id(),
            "lastname" => $order->get_billing_last_name(),
            "firstname" => $order->get_billing_first_name(),
            "email" => $order->get_billing_email(),
            "phone" => $order->get_billing_phone(),
            "state" => $order->get_billing_state(),
            "city" => $order->get_billing_city(),
            "address" => $order->get_billing_address_1() . " " . $order->get_billing_address_2(),
            "discount_code" => $coupons_list,
            "discount" => (empty($order->get_discount) ? 0 : $order->get_discount),
            "shipping" => (empty($order->get_total_shipping) ? 0 : $order->get_total_shipping),
            "total" => $order->get_total()
        );

        if ($this->token && $this->token != '') {

            $orderClient = new Retargeting_REST_API_Client($this->token);
            $orderClient->setResponseFormat("json");
            $orderClient->setDecoding(false);
            $response = $orderClient->order->save($orderInfo, $data['line_items']);

        }

    }

    /*
    * VisitHelpPage
    */
    public function help_pages()
    {
        global $post;
        $page = $post->post_name;
        if (!empty($this->help_pages)) {
            if (in_array($page, $this->help_pages)) {
                echo "<script>
                    var _ra = _ra || {};
                        _ra.visitHelpPageInfo = {
                            'visit' : true
                        }
    
                        if (_ra.ready !== undefined) {
                            _ra.visitHelpPage();
                        }
                </script>";
            }
        }
    }

    /*
    * CheckoutIds
    */
    public function checkout_ids()
    {
        global $woocommerce;
        if ($woocommerce->cart instanceof WC_Cart && count($woocommerce->cart->get_cart() > 0)) {
            $cart_items = $woocommerce->cart->get_cart();
            $line_items = array();
            foreach ($cart_items as $cart_item) {
                $product = $cart_item['data'];
                $line_item = (int)$cart_item['product_id'];
                $line_items[] = $line_item;
            }
            echo '
            <script>
                var _ra = _ra || {};
                _ra.checkoutIdsInfo = ' . json_encode($line_items) . ';

                if (_ra.ready !== undefined) {
                  _ra.checkoutIds(_ra.checkoutIdsInfo);
                }
            </script>';
        }
    }

    /*
     * URL DISCOUNT API
     */
    function retargeting_api_add_query_vars($vars)
    {
        $vars[] = "retargeting";
        $vars[] = "key";
        $vars[] = "value";
        $vars[] = "type";
        $vars[] = "count";
        return $vars;
    }

    function discount_api_template($template)
    {
        global $wp_query;

        if (isset($wp_query->query['retargeting']) && $wp_query->query['retargeting'] == 'discounts') {
            if (isset($wp_query->query['key']) && isset($wp_query->query['value']) && isset($wp_query->query['type']) && isset($wp_query->query['count'])) {
                if ($wp_query->query['key'] != "" && $wp_query->query['key'] == $this->token && $wp_query->query['value'] != "" && $wp_query->query['type'] != "" && $wp_query->query['count'] != "") {
                    //daca totul este ok, genereaza si afiseaza codurile de reducere
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

    /**
     * @param $product
     * @return array
     */
    private function getPricesForVariableProducts($product)
    {
        $prices = $product->get_variation_prices();
        $min_price = current($prices['sale_price']);
        $max_price = end($prices['regular_price']);
        $price = $min_price !== $max_price ? $max_price : $min_price;
        $specialPrice = $min_price !== $max_price ? $min_price : 0;
        $price = wc_get_price_including_tax( $product, array('price' => $price) );
        $specialPrice = wc_get_price_including_tax( $product, array('price' => $specialPrice) );
        return array(
            (!empty($price) ? $price : 0),
            (!empty($specialPrice) ? $specialPrice : 0)
            );
    }

    /**
     * @param $product
     * @return array
     */
    private function getPricesForGroupedProducts($product)
    {
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
}

//genereaza coduri de reducere random

function generate_coupons($count)
{
    global $wp_query;

    $couponChars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $couponCodes = array();
    for ($x = 0; $x < $count; $x++) {
        $couponCode = "";
        for ($i = 0; $i < 8; $i++) {

            $couponCode .= $couponChars[mt_rand(0, strlen($couponChars) - 1)];

        }
        if (woocommerce_verify_discount($couponCode)) {

            woocommerce_add_discount($couponCode, $wp_query->query['value'], $wp_query->query['type']);
            $couponCodes[] = $couponCode;

        } else {
            $x -= 1;
        }

    }
    return json_encode($couponCodes);
}

function woocommerce_verify_discount($code)
{
    global $woocommerce;
    $o = new WC_Coupon($code);
    if ($o->exists == 1) {
        return false;
    } else {

        return true;
    }

}

//adauga coduri in woocommerce

function woocommerce_add_discount($code, $discount, $type)
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