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

/**
 * Class WooCommerceRTGIntegration
 */
class WooCommerceRTGIntegration extends WC_Integration
{
    /**
     * @var array
     */
    protected $rtgParams = [
        'rtg_tracking_key',
        'rtg_rest_key',
        'rtg_cart_btn_id',
        'rtg_price_label_id',
        'rtg_help_pages',
        'rtg_products_feed',
        'rtg_customers_feed'
    ];

    /**
     * @var array
     */
    protected $rtgLinks = [
        'account-api' => [
            '<a href="https://retargeting.biz/admin/module/settings/docs-and-api" target="_blank" rel="noopener noreferrer">',
            '</a>'
        ],
        'implementation-general' => [
            '<a href="https://retargeting.biz/plugins/custom/general" target="_blank" rel="noopener noreferrer">',
            '</a>'
        ],
        'products-feed' => [
            '<a href="https://retargeting.biz/general-implementation-abandoned-cart#javascript-tracking-code" target="_blank" rel="noopener noreferrer">',
            'https://retargeting.biz/general-implementation-abandoned-cart#javascript-tracking-code',
            '</a>'
        ],
        'customers-feed' => [
            '<a href="https://retargeting.biz/general-implementation-abandoned-cart#javascript-tracking-code" target="_blank" rel="noopener noreferrer">',
            'https://retargeting.biz/general-implementation-abandoned-cart#javascript-tracking-code',
            '</a>'
        ]
    ];

    /**
     * WooCommerceRTGIntegration constructor.
     */
    public function __construct()
    {
        $this->id                   = 'rtg_tracker';
        $this->method_title         = "Retargeting Tracker";
        $this->method_description   = __('Retargeting.Biz is a marketing automation tool that boosts the conversion rate and sales of your online store.', 'woo-rtg-tracker');

        $this->setup_requirements();
        $this->init_form_fields();
        $this->init_settings();

        foreach ($this->rtgParams AS $param)
        {
            $this->{$param} = $this->get_option( $param );
        }

        add_action( 'woocommerce_update_options_integration_' .  $this->id, [ $this, 'process_admin_options' ] );

        if (is_admin())
        {
            add_action('admin_notices', [ $this, 'admin_check_for_notices' ]);
        }
        else if(!empty($this->rtg_tracking_key))
        {
            require_once 'class-rtg-tracker.php';

            $options = [];

            foreach ($this->rtgParams AS $param)
            {
                $options[$param] = $this->{$param};
            }

            new WooCommerceRTGTracker($options);
        }
    }

    /**
     * Setup requirements
     */
    public function setup_requirements()
    {
        define('RTG_TRACKER_SETTINGS_URL', get_admin_url() .'/admin.php?page=wc-settings&tab=integration&section=' . $this->id);
    }

    /**
     * Initialise settings form fields.
     */
    public function init_form_fields()
    {
        $this->form_fields = [
            'rtg_tracking_key' => [
                'title'         => __('Tracking API Key', 'woo-rtg-tracker'),
                'type'          => 'text',
                'default'       => ''
            ],
            'rtg_rest_key' => [
                'title'         => __('REST API Key', 'woo-rtg-tracker'),
                'type'          => 'text',
                'description'   => str_replace( ['[link]', '[/link]'], $this->rtgLinks['account-api'], __( 'Both keys can be found in your [link]Retargeting[/link] account.', 'woo-rtg-tracker' ) ),
                'desc_tip'      => false,
                'default'       => ''
            ],
            'rtg_cart_btn_id' => [
                'title'         => __('Add to cart button ID', 'woo-rtg-tracker'),
                'type'          => 'text',
                'description'   => str_replace( ['[link]', '[/link]'], $this->rtgLinks['implementation-general'], __( 'For more info check [link]documentation[/link].', 'woo-rtg-tracker' ) ),
                'desc_tip'      => false,
                'default'       => ''
            ],
            'rtg_price_label_id' => [
                'title'         => __('Price label id', 'woo-rtg-tracker'),
                'type'          => 'text',
                'description'   => str_replace( ['[link]', '[/link]'], $this->rtgLinks['implementation-general'], __( 'For more info check [link]documentation[/link].', 'woo-rtg-tracker' ) ),
                'desc_tip'      => false,
                'default'       => ''
            ],
            'rtg_help_pages' => [
                'title'         => __('Price label id', 'woo-rtg-tracker'),
                'type'          => 'multiselect',
                'options'       => $this->get_pages_map(),
                'description'   => __( 'Choose the pages on which the "visitHelpPage" event should fire.', 'woo-rtg-tracker' ),
                'desc_tip'      => true,
                'default'       => [],
                'css'           => 'min-height: 100px'
            ],
            'rtg_products_feed' => [
                'title'         => __('Products Feed', 'woo-rtg-tracker'),
                'type'          => 'checkbox',
                'description'   => __( 'URL:', 'woo-rtg-tracker' ) . ' ' . implode('', $this->rtgLinks['products-feed']),
                'desc_tip'      => false,
                'label'         => __( 'Enable products feed', 'woo-rtg-tracker' ),
                'default'       => 'no'
            ],
            'rtg_customers_feed' => [
                'title'         => __('Customers Feed', 'woo-rtg-tracker'),
                'type'          => 'checkbox',
                'description'   => __( 'URL:', 'woo-rtg-tracker' ) . ' ' . implode('', $this->rtgLinks['customers-feed']),
                'desc_tip'      => false,
                'label'         => __( 'Enable customers feed', 'woo-rtg-tracker' ),
                'default'       => 'no'
            ]
        ];
    }

    /**
     * Check for admin notices
     */
    public function admin_check_for_notices()
    {
        if (empty($this->rtg_tracking_key))
        {
            $message = __( '%1$sRetargeting tracker for WooCommerce is almost ready.%2$s To complete your configuration, %3$scomplete the setup steps%4$s.', 'woo-rtg-tracker');
            $message = sprintf(
                $message,
                '<strong>', '</strong>',
                '<a href="' . esc_url(RTG_TRACKER_SETTINGS_URL) . '">', '</a>');

            echo $this->get_message_html($message, 'info');
        }
    }

    /**
     * @param $message
     * @param string $type
     * @return false|string
     */
    private function get_message_html($message, $type = 'error') {
        ob_start();

        ?>
        <div class="notice is-dismissible notice-<?php echo $type ?>">
            <p><?php echo $message ?></p>
        </div>
        <?php

        return ob_get_clean();
    }

    /**
     * @return mixed
     */
    private function get_pages_map()
    {
        $pages = [];

        $allPages = get_pages();

        if(!empty($allPages))
        {
            foreach ($allPages as $key => $page)
            {
                $pages[$page->ID] = $page->post_title;
            }
        }

        return $pages;
    }
}