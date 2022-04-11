<?php

define('RTG_TRACKER_SETTINGS_URL', get_admin_url() .'/admin.php?page=wc-settings&tab=integration&section=rtg_tracker');
/**
 * Class WooCommerceRTGIntegration
 */
class WooCommerceRTGIntegration extends WC_Integration
{
    public $id = 'rtg_tracker';
    public $method_title = "Retargeting Tracker";

    /**
     * @var array
     */
    protected $rtgParams = [
        'rtg_tracking_key',
        'rtg_rest_key',
        'rtg_cart_btn_id',
        'rtg_inp_quantity_id',
        'rtg_price_label_id',
        'rtg_help_pages',
        'rtg_products_feed',
        'rtg_products_feed_cron',
        'rtg_customers_feed',
        'rtg_tax_rate'

    ];

    /**
     * @var array
     */
    protected $rtgLinks = [
        'account-api' => [
            '<a href="https://retargeting.app/settings/account/tracking-keys" target="_blank" rel="noopener noreferrer">',
            '</a>'
        ],
        'implementation-general' => [
            '<a href="https://retargeting.biz/plugins/woocommerce" target="_blank" rel="noopener noreferrer">',
            '</a>'
        ],
        'feed' => [
            '<a href="[url]" target="_blank" rel="noopener noreferrer">',
            '[url]',
            '</a>'
        ]
    ];

    /**
     * WooCommerceRTGIntegration constructor.
     */
    public function __construct()
    {

        $this->method_description   = __('Retargeting.Biz is a marketing automation tool that boosts the conversion rate and sales of your online store.', 'woo-rtg-tracker');

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
            include_once RTG_TRACKER_DIR . '/includes/class-rtg-tracker.php';

            new WooCommerceRTGTracker($this);
        }
    }

    /**
     * Initialise settings form fields.
     */
    public function init_form_fields()
    {
        $url = get_site_url(null, '/');
        $customersFeedURL = $url . '?rtg-feed=customers';
        $productsFeedURL  = $url . '?rtg-feed=products';
        $productsFeedCRON  = $url . '?rtg-feed=products-static';

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
            'rtg_inp_quantity_id' => [
                'title'         => __('Add to cart quantity ID: #example | CLASS: .example', 'woo-rtg-tracker'),
                'description'   => __('Default should be "quantity_"', 'woo-rtg-tracker' ),
                'desc_tip'      => false,
                'type'          => 'text',
                'default'       => 'quantity_'
            ],
            'rtg_price_label_id' => [
                'title'         => __('Price label id', 'woo-rtg-tracker'),
                'type'          => 'text',
                'description'   => str_replace( ['[link]', '[/link]'], $this->rtgLinks['implementation-general'], __( 'For more info check [link]documentation[/link].', 'woo-rtg-tracker' ) ),
                'desc_tip'      => false,
                'default'       => ''
            ],
            'rtg_help_pages' => [
                'title'         => __('Visit Help Page', 'woo-rtg-tracker'),
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
                'description'   => __( 'URL:', 'woo-rtg-tracker' ) . ' ' . str_replace('[url]', $productsFeedURL, implode('', $this->rtgLinks['feed'])),
                'desc_tip'      => false,
                'label'         => __( 'Enable products feed', 'woo-rtg-tracker' ),
                'default'       => 'no'
            ],
            'rtg_products_feed_cron' => [
                'title'         => __('Products Feed CRON', 'woo-rtg-tracker'),
                'type'          => 'checkbox',
                'description'   => __( 'URL:', 'woo-rtg-tracker' ) . ' ' . str_replace('[url]', $productsFeedCRON, implode('', $this->rtgLinks['feed'])),
                'desc_tip'      => false,
                'label'         => __( 'Enable products feed Cron', 'woo-rtg-tracker' ),
                'default'       => 'no'
            ],
            'rtg_customers_feed' => [
                'title'         => __('Customers Feed', 'woo-rtg-tracker'),
                'type'          => 'checkbox',
                'description'   => __( 'URL:', 'woo-rtg-tracker' ) . ' ' . str_replace('[url]', $customersFeedURL, implode('', $this->rtgLinks['feed'])),
                'desc_tip'      => false,
                'label'         => __( 'Enable customers feed', 'woo-rtg-tracker' ),
                'default'       => 'no'
            ]
        ];
        if( isset($_POST["woocommerce_rtg_tracker_rtg_products_feed_cron"]) && !wp_next_scheduled( 'RTG_CRON_FEED' ) )
        {
            wp_schedule_event( time(), 'RTG_CRON_SCHEDULES', 'RTG_CRON_FEED' );
        }
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
