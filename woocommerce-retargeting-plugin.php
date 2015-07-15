<?php
/**
 * Plugin Name: WooCommerce Retargeting
 * Plugin URI: https://retargeting.biz/woocommerce-documentation
 * Description: Adds Retargeting Tracking code to WooCommerce.
 * Version: 1.0.0
 * Author: Retargeting Team
 * Author URI: http://retargeting.biz
 * License: GPL2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    exit; 
}

/**
* Check if WooCommerce is active
**/

if(!class_exists('WC_Retargeting_Tracking') ) :
session_start();
class WC_Retargeting_Tracking {
    /*
    * Plugin Version.
    */
    const VERSION = '1.0.0';
    /*
    * Instance of this class
    */
    protected static $instance = null;
    /*
    * Init plugin
    */
    private function __construct(){
	add_action('init', array($this, 'load_plugin_textdomain') );
	//Check if WooCommerce is installed.
	if(class_exists('WC_Integration') && defined('WOOCOMMERCE_VERSION') && version_compare(WOOCOMMERCE_VERSION, '2.2.6', '>=') ) {
	    include_once 'classes/class-wc-retargeting-tracking.php';
	    
	    //Register integration
	    add_filter('woocommerce_integrations', array($this, 'add_integration'));
	} else {
	    add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
	}
    }
    
    /*
    * Return an instance of this class
    */
    public static function get_instance(){
	if(null == self::$instance) {
	    self::$instance = new self;
	}
	return self::$instance;
    }
    /*
    * Load the plugin text domain for translation
    */
    public function load_plugin_textdomain(){
	$locale = apply_filters('plugin_locale', get_locale(), 'woocommerce-retargeting-integration');
	load_textdomain('woocommerce-retargeting-integration', trailingslashit(WP_LANG_DIR) . 'woocommerce-retargeting-integration-' . $locale . '.mo');
	load_plugin_textdomain ('woocommerce-retargeting-integration', false, dirname(plugin_basename( __FILE__ )) . '/languages/');
    }
    /*
    * Fallback notice
    */
    public function woocommerce_missing_notice(){
	echo '<div class="error"><p>'.sprintf(__('WooCommerce Retargeting depends on the last version of %s to work!', 'woocommerce-retargeting-integration'), '<a href="http://www.woothemes.com/woocommerce/" target="_blank">' . __('WooCommerce', 'woocommerce-retargeting-integration') . '</a>').'</p></div>';
    }
    /*
    * Add new integration to WooCommerce
    */
    public function add_integration($integrations) {
	$integrations[] = 'WC_Integration_Retargeting_Tracking';
	return $integrations;
    }
}
add_action('plugins_loaded', array('WC_Retargeting_Tracking', 'get_instance'),0);

endif;

