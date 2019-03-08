<?php
/**
 * Pages Scripts
 */

if (!defined('ABSPATH')) {
    exit;
}

 class WC_Retargeting_Pages_Scripts {

    /**
     *  Creates an array with all parameters 
     *  available for pages
     */

    public function init_retargeting_form_fields()
    {
        // List all pages
        $allPages = get_pages();
        $pages = array();
        foreach ($allPages as $key => $page) {
            $pages['ra_none'] = 'None';
            $pages[$page->post_name] = $page->post_title;
        }

        $this->form_fields = array(
            'domain_api_key' => array(
                'title' => __('Tracking API KEY'),
                'description' => __('Insert Retargeting Tracking API Key. <a href="https://retargeting.biz/admin?action=api_redirect&token=5ac66ac466f3e1ec5e6fe5a040356997" target="_blank" rel="noopener noreferrer">Click here</a> to get your Tracking API Key'),
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
                'description' => __('Select All Help Pages (e.g. How to Order, FAQ, Delivery and Payment, Contact Us)'),
                'type' => 'multiselect',
                'options' => $pages
            ),
        );
    }

    /**
     *  Creates a script with visit parameter assigned to true
     */


    public function help_retargeting_pages($object)
    {
        $page = $object->post_name;
        if (!empty($object->help_pages)) {
            if (in_array($page, $object->help_pages)) {
                $helpScript = "<script type='text/javscript'>
                    var _ra = _ra || {};
                        _ra.visitHelpPageInfo = {
                            'visit' : true
                        }
    
                        if (_ra.ready !== undefined) {
                            _ra.visitHelpPage();
                        }
                </script>";
                return $helpScript;
            }
        }
    }

    /**
     * Takes all item ids, creates an array with them 
     * and assigns this array to a js property checkoutIdsInfo 
     */
    
     public function checkout_retargeting_ids($woocommerce) {
        if ($woocommerce->cart instanceof WC_Cart) {
            $cart_items = $woocommerce->cart->get_cart();
            $line_items = array();
            foreach ($cart_items as $cart_item) {
                //$product = $cart_item['data'];
                $line_item = (int)$cart_item['product_id'];
                $line_items[] = $line_item;
            }
            $idScript = '<script type="text/javascript">
                var _ra = _ra || {};
                _ra.checkoutIdsInfo = ' . json_encode($line_items) . ';

                if (_ra.ready !== undefined) {
                  _ra.checkoutIds(_ra.checkoutIdsInfo);
                }
            </script>';
            return $idScript;
        }
    }
 }
?>