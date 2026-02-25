<?php
/**
 * Plugin Name: Global Zeron orders
 * Description: MVP: Replace WooCommerce My Account orders with a Zeron obligations view (Dummy/Live mode).
 * Version: 0.1.1
 * Author: Global Aesthetics
 * Requires at least: 6.2
 * Requires PHP: 8.1
 * Text Domain: global-zeron-orders
 * Domain Path: /languages
 *
 * @package GlobalZeronOrders
 */

declare(strict_types=1);

if (!defined('ABSPATH')) { exit; }

define('GZO_VERSION', '0.1.1');
define('GZO_PLUGIN_FILE', __FILE__);
define('GZO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GZO_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once GZO_PLUGIN_DIR . 'includes/autoload.php';

register_activation_hook(__FILE__, static function () {
    add_rewrite_endpoint(\GZO\Front\Account::ENDPOINT, EP_ROOT | EP_PAGES);
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, static function () {
    flush_rewrite_rules();
});


add_action('plugins_loaded', static function () {
    load_plugin_textdomain('global-zeron-orders', false, dirname(plugin_basename(__FILE__)) . '/languages');
    \GZO\Plugin::instance()->init();
});
