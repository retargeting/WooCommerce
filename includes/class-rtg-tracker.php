<?php

require_once RTG_TRACKER_DIR . '/vendor/autoload.php';

/**
 * Class WooCommerceRTGTracker
 */
class WooCommerceRTGTracker
{
    /**
     * @var \RetargetingSDK\Javascript\Builder
     */
    private $RTGJSBuilder;

    /**
     * @var \RetargetingSDK\RecommendationEngine
     */
    private $RTGRecEng;

    /**
     * @var array
     */
    private $helpPagesIds = [];
    private $options = [];

    /**
     * WooCommerceRTGTracker constructor.
     * @param $options
     */
    public function __construct($options)
    {

        require_once RTG_TRACKER_DIR . '/includes/models/class-rtg-category-model.php';
        require_once RTG_TRACKER_DIR . '/includes/models/class-rtg-product-model.php';

        $this->options = $options;
        $this->RTGJSBuilder = new \RetargetingSDK\Javascript\Builder();

        $this->RTGJSBuilder->setTrackingApiKey($this->options->rtg_tracking_key);
        $this->RTGJSBuilder->setRestApiKey($this->options->rtg_rest_key);
        $this->RTGJSBuilder->setAddToCardId($this->options->rtg_cart_btn_id);
        $this->RTGJSBuilder->setQuantityInputId($this->options->rtg_inp_quantity_id);
        $this->RTGJSBuilder->setPriceLabelId($this->options->rtg_price_label_id);

        $this->RTGRecEng = new \RetargetingSDK\RecommendationEngine();

        if (is_array($this->options->rtg_help_pages))
        {
            $this->helpPagesIds = $this->options->rtg_help_pages;
        }

        add_action('wp_head',   [ $this, 'header_hook' ]);
        add_action('wp_footer', [ $this, 'footer_hook' ], 9999);

        /* Rec-Engine Zone Start */
        
        // add_action('wp_footer', [ $this, 'footer_rec_hook' ], 9999);
    
        /* Rec-Engine Zone End */
        /* RemoveFromCart */
        // add_action('woocommerce_remove_cart_item', array($this, 'RemoveCartEvent'), 10, 2);
        // add_filter('woocommerce_cart_item_removed_title', array($this, 'RemoveCartEventFilter'), 10, 2);
        /* RemoveFromCart End */
        // woocommerce_before_main_content
        // woocommerce_after_add_to_cart_button
        // woocommerce_after_add_to_cart_quantity
        add_action('wp_footer',       [ $this, 'category_hook' ]);
        add_action('woocommerce_before_single_product',     [ $this, 'product_hook' ], 2);

        add_action('wp_footer',      [ $this, 'add_to_cart_v2_hook']);
        // add_action('woocommerce_remove_cart_item',          [ $this, 'remove_from_cart_hook' ]);
        add_action('woocommerce_after_cart',                [ $this, 'cart_hook' ]);
        add_action('woocommerce_after_checkout_form',       [ $this, 'checkout_hook' ]);
        add_action('woocommerce_thankyou',                  [ $this, 'order_hook' ]);
        add_filter('woocommerce_loop_add_to_cart_link', [ $this, 'add_to_cart_v3_hook'], 10, 3 );
        // add_action('wp_footer',                             [ $this, 'order_hook' ]);
    }

    /* Rec-Engine Zone Start */
    // private static $rec_engine = array(
    //     "is_home" => "rtg_rec_home_page",
    //     "is_order_received_page" => "rtg_rec_thank_you_page", /* Importanta Ordinea */
    //     "is_checkout" => "rtg_rec_shopping_cart",
    //     "is_cart" => "rtg_rec_shopping_cart",
    //     "is_product_category" => "rtg_rec_category_page",
    //     "is_product" => "rtg_rec_product_page",
    //     "is_search" => "rtg_rec_search_page",
    //     "is_404" => "rtg_rec_page_404",
    //     "is_shop" => "rtg_rec_category_page"
    // );

    // function rec_engine_load() {

    //     $add = "";
    //     if ((bool) $this->options->rtg_status && (bool) $this->options->rtg_rec_status) {
    //         foreach (self::$rec_engine as $key=>$value) {
    //             if ($key() || $key === 'is_home' && is_front_page()) {
    //                 $add = '_ra_rec_engine.list = '.str_replace('\\\"', '"', json_encode($this->options->{$value})).';
    //                 _ra_rec_engine.init();';
    //                 break;
    //             }
    //         }

    //         return (
    //             empty($add) ?
    //                 $add : '
    //         var _ra_rec_engine = {};

    //         _ra_rec_engine.init = function () {
    //             let list = this.list;
    //             for (let key in list) {
    //                 _ra_rec_engine.insert(list[key].value, list[key].selector, list[key].place);
    //             }
    //         };

    //         _ra_rec_engine.insert = function (code = "", selector = null, place = "before") {
    //             if (code !== "" && selector !== null) {
    //                 let newTag = document.createRange().createContextualFragment(code);
    //                 let content = document.querySelector(selector);

    //                 content.parentNode.insertBefore(newTag, place === "before" ? content : content.nextSibling);
    //             }
    //         };
    //         '.$add
    //         );

    //     }

    //     return $add;
    // }

    // public function footer_rec_hook()
    // {
    //     // $this->RTGJSBuilder->removeFromCart();
    //     $rm = "";
        
    //     if(!is_ajax() && !isset($_GET['removed_item'])){
    //         $last = [];
        
    //         foreach (self::getSession('RemoveCartEvent') as $cart) {
    //             $rm .= "_ra.removeFromCart(".$cart['product_id'].",".$cart['quantity'].",".$cart['variation'].");";
    //             $last = $cart;
    //         }

    //         if (!empty($rm)) {
    //             $rm = "var _ra = _ra || {};
    //             _ra.removeFromCartInfo = ".json_encode($last)."
    //             if (_ra.ready !== undefined) {
    //             ".$rm."
    //             }";
    //         }
    //     }

    //     echo '<script type="text/javascript">'. $this->rec_engine_load() . $rm .'</script>';
    // }
    /* Rec-Engine Zone Stop */

    /* RemoveFromCart */
    // public static function RemoveCartEvent($item, $cart = null) {
    //     $cart = $cart->cart_contents[$item];
    //     self::addToSession('RemoveCartEvent', array(
    //         "product_id" => $cart['product_id'],
    //         "quantity" => $cart['quantity'],
    //         "variation" => false
    //     ));
    //     // $cart['product_id'], $cart['quantity'], $cart['variation_id'];
    // }

    // public static function RemoveCartEventFilter($item, $cart = null) {
    //     self::addToSession('RemoveCartEvent', array(
    //         "product_id" => $cart['product_id'],
    //         "quantity" => $cart['quantity'],
    //         "variation" => false
    //     ));
    //     // $cart['product_id'], $cart['quantity'],$cart['variation_id'];
    //     return $item;
    // }

    // public static function addToSession($name, $data, $key = null) {
    //     $add = WC()->session->get($name);

    //     if ($key === null)
    //     {
    //         $n = '';

    //         for ($i = 0; $i < 5; ++$i) {
    //             $n .= random_int(0, 9);
    //         }

    //         $add[time().$n] = $data;
    //     } else {
    //         $add[$key] = $data;
    //     }

    //     WC()->session->set($name, $add);
    // }

    // public static function getSession($name = 'RemoveCartEvent') {
    //     $return = WC()->session->get($name);
    //     WC()->session->set($name, array());
    //     return $return;
    // }

    /* RemoveFromCart End*/

    function add_to_cart_v3_hook( $add_to_cart_html, $product, $args = null){
        $before = ''; // Some text or HTML here
        $after = ''; // Add some text or HTML here as well

        $RTGProduct = new WooCommerceRTGProductModel($product);
        $variation = new \RetargetingSDK\Variation();
        $variation = $variation->getData(false);

        $quantityInputId = $this->RTGJSBuilder->getQuantityInputId();

        $productId = $RTGProduct->getId();
        $quantity = 1; // default
        $addToCart = json_encode([
            'product_id' => $productId,
            'quantity' => $quantity,
            'variation' => !empty($variation['code']) ? $variation : false
        ]);
        
        if (empty($quantityInputId)) {
            $quantityInputId = "quantity_";
        }

        $addToCartSelector = "document.querySelectorAll('.add_to_cart_button[data-product_id=\"'+addToCartInfo.product_id+'\"]')";

        $quantitySelector = "document.querySelector(\"input[id ^= '$quantityInputId']\")";

        $after = "
                <script async>
                window.addEventListener('load', 
                function(ev){
                    if(_ra === undefined) {
                        _ra = _ra || {};
                    }
                    let addToCartInfo = $addToCart;
                    
                    if($addToCartSelector !== null) {
                        for (let i in Object.keys($addToCartSelector)) { 
                            ".$addToCartSelector."[i].addEventListener(\"click\", function() {
                                _ra.addToCartInfo = addToCartInfo;

                                if($quantitySelector !== null) { _ra.addToCartInfo.quantity = ".$quantitySelector.".value; }
                                _ra.addToCart(_ra.addToCartInfo.product_id, _ra.addToCartInfo.quantity, _ra.addToCartInfo.variation);
                            });
                        }
                    }
                    
                });
                </script>";
        return $before . $add_to_cart_html . $after;
    }

    /**
     * Add to cart v2 hook
     */
    public function add_to_cart_v2_hook()
    {
        $RTGProduct = new WooCommerceRTGProductModel();
        $variation = new \RetargetingSDK\Variation();
        $variation = $variation->getData(false);

        $addToCartButtonId = $this->RTGJSBuilder->getAddToCardId();
        $quantityInputId = $this->RTGJSBuilder->getQuantityInputId();

        $productId = $RTGProduct->getId();
        $quantity = 1; // default
        $addToCart = json_encode([
            'product_id' => $productId,
            'quantity' => $quantity,
            'variation' => !empty($variation['code']) ? $variation : false
        ]);
        
        if (empty($quantityInputId)) {
            $quantityInputId = "quantity_";
        }

        $addToCartButtonId = $addToCartButtonId !== "" ? $addToCartButtonId : ".single_add_to_cart_button";
        $addToCartSelector = "document.querySelectorAll('$addToCartButtonId')";

        $quantitySelector = "document.querySelector(\"input[id ^= '$quantityInputId']\")";

        echo "
                <script async>
                window.addEventListener('load', 
                function(ev){
                    if(_ra === undefined) {
                        _ra = _ra || {};
                    }

                    let addToCartInfo = $addToCart;

                    if($addToCartSelector !== null) {
                        for (let i in Object.keys($addToCartSelector)) { 
                            ".$addToCartSelector."[i].addEventListener(\"click\", function() {
                                _ra.addToCartInfo = addToCartInfo;

                                if($quantitySelector !== null) { _ra.addToCartInfo.quantity = ".$quantitySelector.".value; }
                                _ra.addToCart(_ra.addToCartInfo.product_id, _ra.addToCartInfo.quantity, _ra.addToCartInfo.variation);
                            });
                        }
                    }
                    
                });
                </script>";
    }

    /**
     * Header hook
     */
    public function header_hook()
    {
        echo '<script type="text/javascript">' . $this->RTGJSBuilder->getTrackingCode() . '</script>';
    }

    /**
     * Footer hook
     *
     * @throws \RetargetingSDK\Exceptions\RTGException
     */
    public function footer_hook()
    {
        $itemId = get_the_ID();

        if (!empty($itemId))
        {
            if (in_array($itemId, $this->helpPagesIds))
            {
                $this->RTGJSBuilder->visitHelpPage();
            }
            elseif ($itemId == get_option('page_on_front'))
            {
                $this->RTGRecEng->markHomePage();
                $this->RTGJSBuilder->visitHomePage();
            }
        }
        else
        {
            $searchQuery = get_search_query( true );

            if (!empty($searchQuery))
            {
                $this->RTGRecEng->markSearchPage();
                $this->RTGJSBuilder->sendSearchTerm($searchQuery);
            }
        }

        echo $this->RTGRecEng->generateTags() . $this->RTGJSBuilder->generate();
    }

    /**
     * Category hook
     *
     * @throws Exception
     */
    public function category_hook()
    {
        require_once RTG_TRACKER_DIR . '/includes/models/class-rtg-category-model.php';

        $RTGCategory = new WooCommerceRTGCategoryModel();

        if($RTGCategory->getId() === '-1')
		{
			$ctID = get_queried_object();
			
			$category = $ctID !== null ? get_term_by('name', $ctID->post_title, 'product_cat') : null;
            $category = empty($category->term_id) ?
				get_term_by('name', get_post($ctID->post_parent)->post_title, 'product_cat') : $category;
            
			if(!empty($category)){
				$RTGCategory = new WooCommerceRTGCategoryModel($category);
			}
		}

        if($RTGCategory->getId() != '-1')
        {
            $this->RTGJSBuilder->sendCategory($RTGCategory);
            $this->RTGRecEng->markCategoryPage();
        }
    }

    /**
     * Product hook
     *
     * @throws Exception
     */
    public function product_hook()
    {
        require_once RTG_TRACKER_DIR . '/includes/models/class-rtg-category-model.php';
        require_once RTG_TRACKER_DIR . '/includes/models/class-rtg-product-model.php';

        $RTGProduct = new WooCommerceRTGProductModel();

        if(!empty($RTGProduct->getId()))
        {
            $this->RTGJSBuilder->sendProduct($RTGProduct);
            $this->RTGJSBuilder->likeFacebook($RTGProduct->getId());
            $this->RTGRecEng->markProductPage();

        }
    }

    /**
     * Add to cart hook
     *
     * @param $cartItemKey
     */
    public function add_to_cart_hook($cartItemKey)
    {
        $RTGProduct = new WooCommerceRTGProductModel();
        $this->RTGJSBuilder->addToCart($RTGProduct->getId(), 1, new \RetargetingSDK\Variation());
    }

    /**
     * Remove from cart hook
     *
     * @param $cartItemKey
     */
    // public function remove_from_cart_hook($cartItemKey)
    // {
    //     $cartItem = WC()->cart->get_cart_item($cartItemKey);

    //     if (!empty($cartItem))
    //     {
    //         $this->RTGJSBuilder->removeFromCart($cartItem['product_id'], $cartItem['quantity'], new \RetargetingSDK\Variation());
    //     }
    // }

    /**
     * Cart hook
     */
    public function cart_hook()
    {
        require_once RTG_TRACKER_DIR . '/includes/models/class-rtg-checkout-model.php';

        $RTGCheckout = new WooCommerceRTGCheckoutModel();

        if (!empty($RTGCheckout->getProductIds()))
        {
            $this->RTGJSBuilder->checkoutIds($RTGCheckout);
        }

        $this->RTGJSBuilder->setCartUrl(wc_get_cart_url());
    }

    /**
     * Checkout hook
     */
    public function checkout_hook()
    {
        require_once RTG_TRACKER_DIR . '/includes/models/class-rtg-checkout-model.php';

        $RTGCheckout = new WooCommerceRTGCheckoutModel();

        if (!empty($RTGCheckout->getProductIds()))
        {
            $this->RTGJSBuilder->checkoutIds($RTGCheckout);
        }

        $this->RTGRecEng->markCheckoutPage();
    }

    /**
     * Order hook
     *
     * @param $orderId
     */
    private $thankYou = true;
    public function order_hook($orderId)
    {
        if ($this->thankYou) {
            $pagename = get_query_var('pagename');
            if ($pagename === "checkout" && empty($orderId)) {
                global $wp;
                if(!empty($wp->query_vars['order-received'])){
                    $orderId = $wp->query_vars['order-received'];
                }else{
                    $orderId = null;
                }
            }
            
            if ($orderId !== null) {
                require_once RTG_TRACKER_DIR . '/includes/models/class-rtg-order-model.php';

                $RTGOrder = new WooCommerceRTGOrderModel($orderId);

                if (!empty($RTGOrder->getOrderNo()))
                {
                    $this->RTGJSBuilder->saveOrder($RTGOrder);
                    $this->RTGRecEng->markThankYouPage();
                }
            }
            $this->thankYou = false;
        }
    }
}