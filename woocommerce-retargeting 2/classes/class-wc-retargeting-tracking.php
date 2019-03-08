<?php

/**
 * Exit if accessed directly
 **/
ini_set("display_errors", 1);
error_reporting(E_ALL);



if (!defined('ABSPATH')) {
    exit;
}

require_once(dirname(__FILE__) . '/../lib/Retargeting_REST_API_Client.php');
require_once(dirname(__FILE__). '/../vendor/autoload.php');

class WC_Integration_Retargeting_Tracking extends WC_Integration
{
    protected $retargetingTracking;
    protected $retargetingCartScript;
    protected $retargetingImage;
    protected $retargetingEmail;
    protected $retargetingMedia;
    protected $retargetingOrder;
    protected $retargetingPage;
    protected $retargetingProduct;

    protected $wpquery;
    protected $woocommerce;
    protected $wcproduct;
    //protected $wp_post;

    protected static $product_type = array('simple', 'variable', 'grouped');

    /*
    * Construct
    */
    public function __construct()
    {
        $this->id = 'retargeting';
        $this->method_title = "Retargeting Tracker";
        $this->method_description = __('Retargeting.Biz is a marketing automation tool that boosts the conversion rate and sales of your online store.');

        $this->init_form_fields();
        $this->init_settings();
        $this->domain_api_key = $this->get_option('domain_api_key');
        $this->token = $this->get_option('token');
        $this->add_to_cart_button_id = $this->get_option('add_to_cart_button_id');
        $this->price_label_id = $this->get_option('price_label_id');
        $this->help_pages = $this->get_option('help_pages');

        /**
         *  Retargeting objects 
         */

        $this->retargetingTracking = new WC_Retargeting_Tracking_Scripts();
        $this->retargetingCartScript = new WC_Retargeting_Cart_Scripts();
        $this->retargetingImage= new WC_Retargeting_Image_Scripts();
        $this->retargetingEmail = new WC_Retargeting_Email_Scripts();
        $this->retargetingMedia = new WC_Retargeting_Media_Scripts();
        $this->retargetingOrder = new WC_Retargeting_Order_Scripts();
        $this->retargetingPage = new WC_Retargeting_Pages_Scripts();
        $this->retargetingProduct = new WC_Retargeting_Product_Scripts();
        
        /**
         *  global objects 
         */

        $this->wp_query = new WP_Query();
        $this->woocommerce = new Woocommerce();
        $this->wcproduct = new WC_Product_Simple();
        
        
        add_action('init', array($this, 'ra_session_init'));

        add_action('woocommerce_update_options_integration_retargeting', array($this, 'process_admin_options'));

        add_action('wp_head', array($this, 'get_retargeting_tracking_code'), 999);

        add_action('wp_head', array($this, 'set_email'), 9999);


        add_action('woocommerce_before_main_content', array($this, 'send_category'), 30, 0);

        add_action('woocommerce_before_single_product', array($this, 'send_product'), 20, 0);

        add_action('woocommerce_after_add_to_cart_button', array($this, 'add_to_cart'));
        
        add_action( 'woocommerce_after_cart',array($this, 'remove_to_cart'));
        add_action( 'woocommerce_after_mini_cart', array($this, 'remove_to_cart'));

        add_action('woocommerce_before_single_product', array($this, 'click_image'), 30, 0);

        add_action('woocommerce_before_single_product', array($this, 'like_facebook'), 50, 0);

        add_action('wp_footer', array($this, 'help_pages'), 999, 0);

        add_action('woocommerce_after_cart', array($this, 'checkout_ids'), 90, 0);
        add_action('woocommerce_after_checkout_form', array($this, 'checkout_ids'), 90, 0);

        add_action('woocommerce_thankyou', array($this, 'save_order'));

        add_action('template_redirect', array($this, 'discount_api_template'));
        add_filter('query_vars', array($this, 'retargeting_api_add_query_vars'));
        
    }

    
    public function init_form_fields()
    {
        // List all pages
        $retargetingPage = new WC_Retargeting_Pages_Scripts();
        $fields = $retargetingPage->init_retargeting_form_fields();
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
    *   Retargeting Tracking Code
    */
    public function get_retargeting_tracking_code()
    {
       $this->retargetingTracking->get_retargeting_tracking_code($this);
    }

    /*
    *  setEmail
    */
    public function set_email()
    {
        $script = $this->retargetingEmail->set_retargeting_email($this->woocommerce);
    }

    /*
    * sendCategory
    */
    public function send_category()
    {
        $script = $this->retargetingProduct->send_category($this->wp_query);
        echo $script;
    }

    /*
     * sendProduct
     */
    public function send_product()
    {
        if (is_product()) {
            //global $product;

            $variation_id = get_post_meta($this->id, '_min_regular_price_variation_id', true);

            if ($this->wcproduct instanceof WC_Product && $this->wcproduct->is_type(self::$product_type)) {

                switch ($this->wcproduct->get_type()) {
                    case 'variable':
                        list($price, $specialPrice) = $this->retargetingProduct->getPricesForVariableProducts($this->wcproduct);
                        break;
                    case 'grouped':
                        list($price, $specialPrice) = $this->retargetingProduct->getPricesForGroupedProducts($this->wcproduct);
                        break;
                    default:
                        $price = wc_get_price_including_tax( $this->wcproduct, array('price' => $this->wcproduct->get_regular_price() ) );
                        $salePrice = wc_get_price_including_tax( $this->wcproduct, array('price' => $this->wcproduct->get_price() ) );
                        $salePrice = $price == $salePrice ? 0 : $salePrice;
                        $specialPrice = (!empty($salePrice) ? $salePrice : 0);
                        break;
                }

                $image_url = wp_get_attachment_url(get_post_thumbnail_id());
                if (empty($image_url)) {
                    $image_url = site_url() . '/wp-content/plugins/woocommerce/assets/images/placeholder.png';
                }

                $cat = $this->retargetingProduct->get_retargeting_product_categories($this->wcproduct);

                $stock = $this->wcproduct->is_in_stock() ? 1 : 0;
                $script = $this->retargetingProduct->send_retargeting_product($this->wcproduct, $image_url, $price, $specialPrice, $stock, $cat);
            }
        }
    }

    /*
    * addToCart
    */
    public function add_to_cart()
    {
        $script = $this->retargetingCartScript->add_to_retargeting_cart($this->wcproduct); 
        echo $script;
    }
    
    /*
    * removeFromCart
    */
    public function remove_to_cart()
    {
        $script = $this->retargetingCartScript->remove_from_retargeting_cart();
        echo $script;
    }
    
    /*
    * clickImage
    */
    public function click_image()
    {
        global $product;
        //echo(get_class($product));
        $this->retargetingImage->click_retaregeting_image($product);
        
    }

    /*
    * likeFacebook
    */
    public function like_facebook()
    {
        $this->retargetingMedia->like_retargeting_facebook($this->wcproduct);
    }

    /*
    * saveOrder
    */
    public function save_order($order_id)
    {
        $this->retargetingOrder->save_retargeting_order($order_id, $this); 
    }

    /*
    * visitHelpPage
    */
    public function help_pages()
    {
        global $post;
        //echo(get_class($post));
        $script = $this->retargetingPage->help_retargeting_pages($post);
        echo $script;
    }

    /*
    * checkoutIds
    */
    public function checkout_ids()
    {
        global $woocommerce;
        $script = $this->retargetingPage->checkout_retargeting_ids($woocommerce);
        echo $script;
    }

    /*
     * URL DISCOUNT API
     */
    public function retargeting_api_add_query_vars($vars)
    {
        $vars[] = "retargeting";
        $vars[] = "key";
        $vars[] = "value";
        $vars[] = "type";
        $vars[] = "count";
        return $vars;
    }

    public function discount_api_template()
    {
        $this->retargetingProduct->discount_retargeting_api_template($this->wp_query);
    }

    // Generate random discount codes

    public function generate_coupons($count)
    {
        $script = $this->retargetingProduct->generate_retargeting_coupons($this->wp_query);
        echo $script;
    }
}



