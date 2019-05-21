<?php
/**
 * 2014-2019 Retargeting BIZ SRL
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@retargeting.biz so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Retargeting SRL <info@retargeting.biz>
 * @copyright 2014-2019 Retargeting SRL
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

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
     * @var array
     */
    private $helpPagesIds = [];

    /**
     * WooCommerceRTGTracker constructor.
     * @param $options
     */
    public function __construct($options)
    {
        $this->RTGJSBuilder = new \RetargetingSDK\Javascript\Builder();
        $this->RTGJSBuilder->setTrackingApiKey($options['rtg_tracking_key']);
        $this->RTGJSBuilder->setRestApiKey($options['rtg_rest_key']);
        $this->RTGJSBuilder->setAddToCardId($options['rtg_cart_btn_id']);
        $this->RTGJSBuilder->setPriceLabelId($options['rtg_price_label_id']);

        if (is_array($options['rtg_help_pages']))
        {
            $this->helpPagesIds = $options['rtg_help_pages'];
        }

        add_action('wp_enqueue_scripts', [ $this, 'load_scripts' ]);

        add_action('wp_head',   [ $this, 'header_hook' ]);
        add_action('wp_footer', [ $this, 'footer_hook' ], 9999);

        add_action('woocommerce_before_main_content',       [ $this, 'category_hook' ]);
        add_action('woocommerce_before_single_product',     [ $this, 'product_hook' ], 2);
        add_action('woocommerce_add_to_cart',               [ $this, 'add_to_cart_hook' ]);
        add_action('woocommerce_remove_cart_item',          [ $this, 'remove_from_cart_hook' ]);
        add_action('woocommerce_after_cart',                [ $this, 'cart_hook' ]);
        add_action('woocommerce_after_checkout_form',       [ $this, 'checkout_hook' ]);
        add_action('woocommerce_thankyou',                  [ $this, 'order_hook' ]);
    }

    /**
     * Load scripts hook
     */
    public function load_scripts()
    {
        wp_enqueue_script( 'rtg-tracker', plugin_dir_url( RTG_TRACKER_DIR . '/woocommerce-retargeting.php' ) . '/assets/js/rtg-tracker.js', [ 'jquery' ] );
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
                $this->RTGJSBuilder->visitHomePage();
            }
        }
        else
        {
            $searchQuery = get_search_query( true );

            if (!empty($searchQuery))
            {
                $this->RTGJSBuilder->sendSearchTerm($searchQuery);
            }
        }

        echo $this->RTGJSBuilder->generate();
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

        if($RTGCategory->_hasCategoryData())
        {
            $this->RTGJSBuilder->sendCategory($RTGCategory);
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

        if($RTGProduct->_hasProductData())
        {
            $this->RTGJSBuilder->sendProduct($RTGProduct);
            $this->RTGJSBuilder->likeFacebook($RTGProduct->getId());
        }
    }

    /**
     * Add to cart hook
     *
     * @param $cartItemKey
     */
    public function add_to_cart_hook($cartItemKey)
    {
        $cartItem = WC()->cart->get_cart_item($cartItemKey);

        if (!empty($cartItem))
        {
            $this->RTGJSBuilder->addToCart($cartItem['product_id'], $cartItem['quantity'], new \RetargetingSDK\Variation());
        }
    }

    /**
     * Remove from cart hook
     *
     * @param $cartItemKey
     */
    public function remove_from_cart_hook($cartItemKey)
    {
        $cartItem = WC()->cart->get_cart_item($cartItemKey);

        if (!empty($cartItem))
        {
            $this->RTGJSBuilder->removeFromCart($cartItem['product_id'], $cartItem['quantity'], new \RetargetingSDK\Variation());
        }
    }

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
    }

    /**
     * Order hook
     *
     * @param $orderId
     */
    public function order_hook($orderId)
    {
        require_once RTG_TRACKER_DIR . '/includes/models/class-rtg-order-model.php';

        $RTGOrder = new WooCommerceRTGOrderModel($orderId);

        if (!empty($RTGOrder->getOrderNo()))
        {
            $this->RTGJSBuilder->saveOrder($RTGOrder);
        }
    }
}