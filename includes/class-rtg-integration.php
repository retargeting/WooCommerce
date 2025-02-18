<?php

define('RTG_TRACKER_SETTINGS_URL', get_admin_url() .'/admin.php?page=wc-settings&tab=integration&section=rtg_tracker');
/**
 * Class WooCommerceRTGIntegration
 */

/**
 * @property mixed|string $rtg_rec_status
 * @property mixed|string $rtg_tracking_key
 */
class WooCommerceRTGIntegration extends WC_Integration
{
    public $id = 'rtg_tracker';
    public $method_title = "Retargeting Tracker";

    /**
     * @var array
     */
    private $rtgParams = [
        "rtg_status",
        "rtg_tracking_key",
        "rtg_rest_key",
        "rtg_cart_btn_id",
        "rtg_inp_quantity_id",
        "rtg_price_label_id",
        "rtg_help_pages",
        "rtg_products_feed",
        "rtg_products_feed_cron",
        "rtg_tax_rate",
        // "rtg_rec_status"
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
        
        foreach ($this->rtgParams as $param)
        {
            $this->{$param} = $this->get_option($param);
        }

        foreach ($this->rtgRec as $param) {
            $this->{$param} = $this->get_option($param);
        }

        add_action('woocommerce_update_options_integration_' .  $this->id, [ $this, 'process_admin_options' ] );
        add_filter('woocommerce_settings_api_sanitized_fields_rtg_tracker',  [ $this, 'save_fields']);
        
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
    
    /* Rec-Engine Zone Start */
    // private $rtgRec = [
    //     "rtg_rec_home_page",
    //     "rtg_rec_category_page",
    //     "rtg_rec_product_page",
    //     "rtg_rec_shopping_cart",
    //     "rtg_rec_thank_you_page",
    //     "rtg_rec_search_page",
    //     "rtg_rec_page_404"
    // ];

    // private static $def = array(
    //     "value" => "",
    //     "selector" => ".site-main",
    //     "place" => "after"
    // );

    // private static $blocks = array(
    //     'block_1' => array(
    //         'title' => 'Block 1',
    //         'def_rtg' => array(
    //             "value"=>"",
    //             "selector"=>".site-main",
    //             "place"=>"before"
    //         )
    //     ),
    //     'block_2' => array(
    //         'title' => 'Block 2',
    //     ),
    //     'block_3' => array(
    //         'title' => 'Block 3'
    //     ),
    //     'block_4' => array(
    //         'title' => 'Block 4'
    //     )
    // );

    // private static $fields = [
    //     'home_page' => array(
    //         'title' => 'Home Page',
    //         'type'  => 'rec_engine'
    //     ),
    //     'category_page' => array(
    //         'title' => 'Category Page',
    //         'type'  => 'rec_engine'
    //     ),
    //     'product_page' => array(
    //         'title' => 'Product Page',
    //         'type'  => 'rec_engine'
    //     ),
    //     'shopping_cart' => array(
    //         'title' => 'Shopping Cart',
    //         'type'  => 'rec_engine'
    //     ),
    //     'thank_you_page' => array(
    //         'title' => 'Thank you Page',
    //         'type'  => 'rec_engine'
    //     ),
    //     'search_page' => array(
    //         'title' => 'Search Page',
    //         'type'  => 'rec_engine'
    //     ),
    //     'page_404' => array(
    //         'title' => 'Page 404',
    //         'type'  => 'rec_engine'
    //     )
    // ];

    /* Rec-Engine Zone End */
    public static function save_fields($options) {
        foreach ($options as $key => $option) {
            if (strpos($key, 'rtg_rec_') !== false) {
                $options[$key] = $_POST['woocommerce_rtg_tracker_'.$key];
            }
        }
        return $options;
    }

    // function generate_rec_engine_html($key, $selected) {

	// 	$field_key = $this->get_field_key( $key );
        
    //     $value = isset($_POST['woocommerce_rtg_tracker_'.$key]) ?
    //         $_POST['woocommerce_rtg_tracker_'.$key] :
    //         $this->get_option($key, array());


    //     $html = '<tr valign="top">
	// 		<th scope="row" class="titledesc">
	// 			<label>'.wp_kses_post($selected['title'] ).'</label>
	// 		</th>
	// 		<td class="forminp">';

    //     foreach (self::$blocks as $k => $v) {
    //         if (empty($value[$k]['value']) && empty($value[$k]['selector'])) {
    //             $def = isset($v['def_rtg']) ?
    //                 $v['def_rtg'] : (isset($selected['def_rtg']) ? $selected['def_rtg'] : null);

    //             $value[$k] = $def !== null ? $def : self::$def;
    //         }
    //         $html .= '
    //             <label for="'.esc_attr($field_key).'_'.$k.'">
    //                 <strong>'.$v['title'].'</strong>
    //             </label>
	// 			<fieldset>
    //             <textarea style="max-width:400px;width: 50%; height: 75px;"'.
    //             ' id="'.esc_attr($field_key).'_'.$k.'" name="'.esc_attr($field_key).'['.$k.'][value]" spellcheck="false">'.
    //             (string) str_replace('\"','"',$value[$k]['value']).'</textarea>
	// 			</fieldset>
    //             <p class="description">
    //             <strong><a href="javascript:void(0);" onclick="document.querySelectorAll(\'#'.esc_attr($field_key).
    //             '_advace\').forEach((e)=>{e.style.display=e.style.display===\'none\'?\'table-row\':\'none\';});">'.
    //             'Show/Hide Advance</a></strong>
    //             </p>';

    //         $html .= '<fieldset id="'.esc_attr($field_key).'_advace" style="display:none"><input style="max-width: 300px;width: 70%;display: inline;" class="wc_input input-text regular-input" type="text" name="'.esc_attr($field_key).'['.$k.'][selector]" value="'.$value[$k]['selector'].'" />';
    //         $html .= '<select style="max-width: 100px;width: 30%;max-height: 30px;display: inline;" name="'.esc_attr($field_key).'['.$k.'][place]">';
    //         foreach (['before', 'after'] as $v)
    //         {
    //             $html .= '<option value="'.$v.'"'.($value[$k]['place'] === $v ? ' selected="selected"' : '' );
    //             $html .= '>'.$v.'</option>'."\n";  
    //         }
    //         $html .= '</select></fieldset><br />';
    //     }
    //     $html .= '</td>
    // </tr>';
	// 	return $html;
	// }
    
    // public function load_rec_engine() {
        

    //     foreach (self::$fields as $key => $value) {
    //         $v = "rtg_rec_".$key;
    //         // $this->rtgParams[] = $v;

    //         /*$this->form_fields[$v.'_title'] = array(
	// 			'title' => $value['title'],
	// 			//'type'  => 'title',
    //             'type'  => 'rec_engine',
	// 			//'description' => 'This section is for Recommendation Engine '.$value['title'],
    //             'desc_tip' => false
	// 		);*/

    //         $this->form_fields[$v] = $value;
    //     }
    // }


    /* Rec-Engine Zone End */
    /**
     * Initialise settings form fields.
     */
    public function init_form_fields()
    {
        $url = get_site_url(null, '/');

        $productsFeedURL  = $url . '?rtg-feed=products';
        $productsFeedCRON  = $url . '?rtg-feed=products-static';

        $this->form_fields = [
            'rtg_status' => [
                'title'         => 'Enable',
                'type'          => 'select',
                'options'       => [
                    0 => 'Disable',
                    1 => 'Enable',
                ],
                'default'       => 0
            ],
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
            // 'rtg_rec_status' => [
            //     'title'         => "Recommendation Engine",
            //     'type'          => 'select',
            //     'options'       => [
            //         0 => 'Disable',
            //         1 => 'Enable',
            //     ],
            //     'desc_tip'      => true,
            //     'default'       => 0
            // ]
        ];
        
        // $this->load_rec_engine();
        
        if( isset($_POST["woocommerce_rtg_tracker_rtg_products_feed_cron"]) && !wp_next_scheduled( 'RTG_CRON_FEED' ) ) {
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
