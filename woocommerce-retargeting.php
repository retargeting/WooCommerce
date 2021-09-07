<?php
/**
 * Plugin Name:             WooCommerce Retargeting
 * Plugin URI:              https://retargeting.biz/en/plugins/woocommerce
 * Description:             Retargeting is a marketing automation tool that boosts the conversion rate and sales of your online store.
 * Version:                 3.0.7
 * Author:                  Retargeting Team
 * Author URI:              http://retargeting.biz
 * License:                 GPL2
 * License URI:             https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:             woo-rtg-tracking
 * Domain Path:             /i18n/languages/
 * WC requires at least:    4.0.0
 * WC tested up to:         5.7.2
 *
 * @package WooRetargeting
 */

defined('ABSPATH') OR exit('No direct script access allowed');

if (!defined('RTG_TRACKER_DIR'))
{
    define('RTG_TRACKER_DIR', dirname(__FILE__));
}

if (!class_exists('WooCommerceRTG'))
{
    include_once RTG_TRACKER_DIR . '/includes/class-rtg.php';
}

$wooRTG = new WooCommerceRTG();

register_activation_hook( __FILE__, [$wooRTG, 'rtgInstall'] );
register_deactivation_hook( __FILE__, [$wooRTG, 'rtgDisable'] );