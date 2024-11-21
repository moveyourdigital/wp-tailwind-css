<?php
/**
 * Plugin Name:     Tailwind CSS
 * Plugin URI:      https://github.com/moveyourdigital/wp-tailwind-css
 * Description:     Gutenberg Block Editor with Tailwind CSS
 * Version:         0.1.0
 * Requires:        PHP: 7.4
 * Author:          Move Your Digital, Inc.
 * Author URI:      https://moveyourdigital.com
 * License:         GPLv2
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:      https://github.com/moveyourdigital/wp-tailwind-css/raw/main/wp-info.json
 * Text Domain:     tailwind-css
 * Domain Path:     /languages
 *
 * @package         tailwind-css
 */

/*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

namespace Tailwind_CSS;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Use any URL path relative to this plugin
 *
 * @param string $path the path.
 * @return string
 */
function plugin_uri( $path ) {
	return plugins_url( $path, __FILE__ );
}

/**
 * Use any directory relative to this plugin
 *
 * @since 0.1.0
 * @param string $path the path.
 * @return string
 */
function plugin_dir( $path ) {
	return plugin_dir_path( __FILE__ ) . $path;
}

/**
 * Gets the plugin unique identifier
 * based on 'plugin_basename' call.
 *
 * @since 0.1.0
 * @return string
 */
function plugin_file() {
	return plugin_basename( __FILE__ );
}

/**
 * Gets the plugin basedir
 *
 * @since 0.1.0
 * @return string
 */
function plugin_slug() {
	return dirname( plugin_file() );
}

/**
 * Gets the plugin version.
 *
 * @since 0.1.0
 * @param bool $revalidate force plugin revalidation.
 * @return string
 */
function plugin_data( bool $revalidate = false ) {
	if ( true === $revalidate ) {
		delete_transient( 'plugin_data_' . plugin_file() );
	}

	$plugin_data = get_transient( 'plugin_data_' . plugin_file() );

	if ( ! $plugin_data ) {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugin_data = get_plugin_data( __FILE__ );
		$plugin_data = array_intersect_key(
			$plugin_data,
			array_flip(
				array( 'Version', 'UpdateURI' )
			)
		);

		set_transient( 'plugin_data' . plugin_file(), $plugin_data );
	}

	return $plugin_data;
}

/**
 * Get plugin version
 *
 * @return string|null
 */
function plugin_version() {
	$data = plugin_data();

	if ( isset( $data['Version'] ) ) {
		return $data['Version'];
	}
}

/**
 * Get plugin update URL
 *
 * @return string|null
 */
function plugin_update_uri() {
	$data = plugin_data();

	if ( isset( $data['UpdateURI'] ) ) {
		return $data['UpdateURI'];
	}
}

/**
 * Capability to manage Tailwind CSS configs
 *
 * @return string
 */
function manage_capability() {
	return 'manage_tailwind_config';
}

/**
 * Load plugin translations and post type
 *
 * @since 0.1.0
 */
add_action(
	'init',
	function () {
		load_plugin_textdomain(
			'tailwind-css',
			false,
			plugin_slug() . '/languages/'
		);

		if ( is_admin() ) {
			include __DIR__ . '/inc/admin.php';
		}

		if ( is_multisite() ) {
			include __DIR__ . '/inc/network.php';
		}

		include __DIR__ . '/inc/updater.php';
	}
);

/**
 * Add capabilities to administrator
 *
 * @since 0.1.0
 */
register_activation_hook(
	__FILE__,
	function () {
		$role = get_role( 'administrator' );
		$role->add_cap( manage_capability() );
	}
);

/**
 * Remove capabilities from administrator
 *
 * @since 0.1.0
 */
register_deactivation_hook(
	__FILE__,
	function () {
		$role = get_role( 'administrator' );
		$role->remove_cap( manage_capability() );
	}
);
