<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://profiles.wordpress.org/ankitmaru
 * @since      1.0.0
 *
 * @package    Isams_Events_Sync_By_Waytocode
 * @subpackage Isams_Events_Sync_By_Waytocode/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Isams_Events_Sync_By_Waytocode
 * @subpackage Isams_Events_Sync_By_Waytocode/includes
 * @author     Ankit Panchal <ankitmaru@live.in>
 */
class Isams_Events_Sync_By_Waytocode_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'isams-events-sync-by-waytocode',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
