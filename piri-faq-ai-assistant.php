<?php
/**
 * Piri FAQ AI Assistant
 *
 *
 * @package   Piri-FAQ-AI-Assistant
 * @author    PIRI
 * @license   GPL-3.0
 * @link      http://piri.ai/
 * @copyright 2019 PIRI
 *
 * @wordpress-plugin
 * Plugin Name:       Piri FAQ AI Assistant
 * Plugin URI:        http://piri.ai/
 * Description:       FAQ AI Assistant
 * Version:           1.2.2
 * Author:            PIRI
 * Author URI:        http://piri.ai/
 * Text Domain:       piri-faq-ai-assistant
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path:       /languages
 */


namespace Piri\FAA;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PIRI_FAA_VERSION', '1.2.2' );


/**
 * Autoloader
 *
 * @param string $class The fully-qualified class name.
 * @return void
 *
 *  * @since 1.0.0
 */
spl_autoload_register(function ($class) {

    
    // project-specific namespace prefix
    $prefix = __NAMESPACE__;

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/includes/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    
    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

/**
 * Initialize Plugin
 *
 * @since 1.0.0
 */
function init() {
    
	$wpr = Plugin::get_instance();
    $wpr_admin = Admin::get_instance();
    $wpr_admin = Assistant::get_instance();
}
add_action( 'plugins_loaded', 'Piri\\FAA\\init' );


/**
 * Register activation and deactivation hooks
 */
register_activation_hook( __FILE__, array( 'Piri\\FAA\\Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Piri\\FAA\\Plugin', 'deactivate' ) );

