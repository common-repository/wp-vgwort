<?php
/**
 * Plugin Name: Prosodia VGW OS
 * Plugin URI: https://wordpress.org/plugins/wp-vgwort/
 * Description: Verdienen Sie mit Ihren Beiträgen/Texten Geld durch die Integration von Zählmarken der VG WORT.
 * Version: 3.25.3
 * Author: Prosodia – Verlag für Musik und Literatur
 * Author URI: https://prosodia.de/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Text Domain: wpvgw
 * Domain Path: /languages
 *
 * @author    Dr. Ronny Harbich <ronny@developer.falconiform.de>, Marcus Franke <wgwortplugin@mywebcheck.de>
 * @license   GPLv2 or later
 * @link      https://prosodia.de/
 * @copyright Prosodia
 */


// exit if file is accessed directly (outside from wordpress)
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * The global plugin namespace.
 */
define( 'WPVGW', 'wpvgw' );

/**
 * The global plugin version.
 */
define( 'WPVGW_VERSION', '3.25.3' );

/**
 * The global plugin path (with trailing slash).
 */
define( 'WPVGW_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * The global relative plugin path (without trailing slash).
 */
define( 'WPVGW_PLUGIN_PATH_RELATIVE', dirname( plugin_basename( __FILE__ ) ) );

/**
 * The global plugin main file path.
 */
define( 'WPVGW_PLUGIN_FILE_PATH', __FILE__ );

/**
 * The plugin URL. The URL has no trailing slash.
 */
define( 'WPVGW_PLUGIN_URL', plugins_url( '', __FILE__ ) );

/**
 * The base name of the plugin. Something like "my-plugin/my-plugin.php".
 */
define( 'WPVGW_PLUGIN_BASE_NAME', plugin_basename( __FILE__ ) );

/**
 * The slug of the plugin. Something like "my-plugin".
 */
define( 'WPVGW_PLUGIN_SLUG', 'wp-vgwort' );

/**
 * The name of the plugin. Something like "My Plugin".
 */
define( 'WPVGW_PLUGIN_NAME', 'Prosodia VGW OS' );

/**
 * The global plugin text domain (need for translation).
 */
define( 'WPVGW_TEXT_DOMAIN', WPVGW );


// include all plugin files
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'main.php' );

/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'includes/options-base.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'includes/options.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'includes/user-options.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'includes/long-task.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'includes/markers-manager.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'includes/helper.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'includes/posts-extras.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'includes/database-data-retriever.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'includes/admin-views-manager.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'includes/uncached-wp-query.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'includes/cache.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'includes/shortcodes.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'includes/mysql-limit-select.php' );

/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'rest-api/rest-api.php' );

/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'views/view-base.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'views/markers-table.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'views/post-view.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'views/post-table-view.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'views/admin/admin-view-base.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'views/admin/markers-admin-view.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'views/admin/import-admin-view.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'views/admin/configuration-admin-view.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'views/admin/operations-admin-view.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'views/admin/data-privacy-admin-view.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'views/admin/support-admin-view.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'views/admin/about-admin-view.php' );

/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'plugin-extensions/plugin-extension.php' );
/** @noinspection PhpIncludeInspection */
require_once( WPVGW_PLUGIN_PATH . 'plugin-extensions/advanced-custom-fields-plugin-extension.php' );


// run the plugin
WPVGW_Main::get_instance();
