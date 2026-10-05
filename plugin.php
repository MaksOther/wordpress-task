<?php
/**
 * Plugin Name:       Auth Forms for Elementor
 * Description:       Elementor widget with AJAX login, registration and lost-password forms.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.1
 * Requires Plugins:  elementor
 * License:           GPL-2.0-or-later
 * Text Domain:       auth-forms-for-elementor
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AFE_VERSION', '1.0.0' );
define( 'AFE_FILE', __FILE__ );
define( 'AFE_PATH', plugin_dir_path( __FILE__ ) );
define( 'AFE_URL', plugin_dir_url( __FILE__ ) );
define( 'AFE_MIN_ELEMENTOR_VERSION', '3.5.0' );

require_once AFE_PATH . 'includes/functions.php';

require_once AFE_PATH . 'classes/class-afe-plugin.php';
require_once AFE_PATH . 'classes/class-afe-settings.php';
require_once AFE_PATH . 'classes/class-afe-recaptcha.php';
require_once AFE_PATH . 'classes/class-afe-assets.php';
require_once AFE_PATH . 'classes/class-afe-form.php';

require_once AFE_PATH . 'ajax/class-afe-ajax.php';
require_once AFE_PATH . 'ajax/class-afe-ajax-login.php';
require_once AFE_PATH . 'ajax/class-afe-ajax-register.php';
require_once AFE_PATH . 'ajax/class-afe-ajax-lost-password.php';

function afe_init() {
	AFE_Plugin::instance()->init();
}
add_action( 'plugins_loaded', 'afe_init' );
