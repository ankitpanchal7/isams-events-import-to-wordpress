<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://profiles.wordpress.org/ankitmaru
 * @since             1.0.0
 * @package           Isams_Events_Sync_By_Waytocode
 *
 * @wordpress-plugin
 * Plugin Name:       iSams Events Sync By WaytoCode
 * Plugin URI:        https://www.waytocode.com
 * Description:       This plugin imports all Events from the iSAMS and sync with WordPress.
 * Version:           1.0.0
 * Author:            Ankit Panchal
 * Author URI:        https://profiles.wordpress.org/ankitmaru
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       isams-events-sync-by-waytocode
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ISAMS_EVENTS_SYNC_BY_WAYTOCODE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-isams-events-sync-by-waytocode-activator.php
 */
function activate_isams_events_sync_by_waytocode() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-isams-events-sync-by-waytocode-activator.php';
	Isams_Events_Sync_By_Waytocode_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-isams-events-sync-by-waytocode-deactivator.php
 */
function deactivate_isams_events_sync_by_waytocode() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-isams-events-sync-by-waytocode-deactivator.php';
	Isams_Events_Sync_By_Waytocode_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_isams_events_sync_by_waytocode' );
register_deactivation_hook( __FILE__, 'deactivate_isams_events_sync_by_waytocode' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-isams-events-sync-by-waytocode.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_isams_events_sync_by_waytocode() {

	$plugin = new Isams_Events_Sync_By_Waytocode();
	$plugin->run();

}
run_isams_events_sync_by_waytocode();

function isa_add_cron_recurrence_interval( $schedules ) {
    $schedules['every_one_day'] = array(
            'interval'  => 86400,
            'display'   => __( 'Every 1 Day', 'textdomain' )
    );
     
    return $schedules;
}
add_filter( 'cron_schedules', 'isa_add_cron_recurrence_interval' );

register_activation_hook(__FILE__, 'isams_deactivation_hook');
function isams_deactivation_hook() {
    if (! wp_next_scheduled ( 'isams_daily_event' )) {
		wp_schedule_event(time(), 'every_one_day', 'isams_daily_event');
    }
}

// add_action('isams_daily_event', 'do_this_everyday');

function do_this_everyday() {
	
	global $wpdb;

	$settings = get_option( 'isams_settings_option_name' );
	$startDate = $settings["start_date_1"];
	$endDate = $settings["end_date_2"];

	$apiURL = $settings["api_url_3"]."?"."apiKey=".$settings["isams_api_key_0"];
	$filters = '<Filters> \n  <CalendarManager> \n    <Events StartDate=\"'.$startDate.'\" EndDate=\"'.$startDate.'\" /> \n  </CalendarManager> \n</Filters>';
	
	$rowData = json_decode( get_categories_from_isams( $apiURL, $startDate, $endDate ) );
	$categories = $rowData->iSAMS->CalendarManager->Categories->Category;

	foreach( $categories as $category ) {

		// $term_isams_id = 0;
		foreach( $category as $ids ){ $term_isams_id = $ids; break; }	
		$slug = strtolower(str_replace(" ", "-", $category->Name));
		
		if( $term_isams_id == 4 ) {
			$args = array(
				'hide_empty' => false,
				'meta_query' => array(
				    array(
				       'key'       => 'isams_cat_id',
				       'value'     => $term_isams_id,
				       'compare'   => '='
				    )
				),
				'taxonomy'  => 'event_type',
			);
			$terms = get_terms( $args );

			if( count($terms) == 0 ) {
				$term_id = wp_insert_term(
				  $category->Name, // the term 
				  'event_type', // the taxonomy
				  array(
				    'description'=> $category->Name,
				    'slug' => $slug,
				  )
				);
				update_term_meta( $term_id['term_id'], 'isams_cat_id', $term_isams_id );
			}
		}

	}

	$settings = get_option( 'isams_settings_option_name' );
	$startDate = $settings["start_date_1"];
	$endDate = $settings["end_date_2"];

	$apiURL = $settings["api_url_3"]."?"."apiKey=".$settings["isams_api_key_0"];
	$filters = '<Filters> \n  <CalendarManager> \n    <Events StartDate=\"'.$startDate.'\" EndDate=\"'.$startDate.'\" /> \n  </CalendarManager> \n</Filters>';
	
	$rowData = json_decode( get_events_from_isams( $apiURL, $startDate, $endDate ) );
	$events = $rowData->iSAMS->CalendarManager->Events;

	if( count($events) > 0 ) {
		$existingEventsObj = get_posts( array( 'post_type' => 'ajde_events', 'numberposts' => -1));
	    foreach( $existingEventsObj as $myevent ) {
	        wp_delete_post( $myevent->ID, true);
	    }
	}
	foreach( $events->Event as $Event ) :
		$term_id = 0;
		$cnt = 0;
		foreach( $Event as $id ){ 
			if( $cnt == 0 )
				$event_id = $id;
			if( $cnt == 1 )
				$term_id = $id;
			if( $cnt > 1 )
				break;

			$cnt++;
		}
		$cc = 1;
		
		if( $term_id == 4 ){ 

			$existingEvent = $wpdb->get_var("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'isams_event_id' AND  meta_value = $event_id LIMIT 1");

			$args = array(
				'hide_empty' => false,
				'meta_query' => array(
				    array(
				       'key'       => 'isams_cat_id',
				       'value'     => $term_id,
				       'compare'   => '='
				    )
				),
				'taxonomy'  => 'event_type',
			);
			$terms = get_terms( $args );

			$StartDate = $Event->StartDate;
			$AllDayEvent = ( $Event->AllDayEvent == 0 ) ? 'no' : 'yes';
			$StartTime = $Event->StartTime;
			$EndTime = $Event->EndTime;
			$Description = $Event->Description;
			$Location = $Event->Location;

			$sTime = end(explode("T",$StartTime));
			$eTime = end(explode("T",$EndTime));
			$dateStartAr = explode("T",$StartDate);
			$dateStart = $dateStartAr[0];

			$sRow = $dateStart." ".$sTime;
			$eRow = $dateStart." ".$eTime;
			$evYear = date("Y",strtotime($StartDate));

			$evcal_srow = strtotime( date('Y-m-d H:i:s', strtotime($sRow)) );
			$evcal_erow = strtotime( date('Y-m-d H:i:s', strtotime($eRow)) );
			$evcal_subtitle = date('l, F jS,', $evcal_srow)." ".date("h:i a",$evcal_srow)." - ".date("h:i a",$evcal_erow);

			if( isset($existingEvent) && !empty($existingEvent) ) {

				$updateEvent = array(
				  'ID'           => $existingEvent,
				  'post_title'   => $Description,
				  'post_content' => $Description,
				);
				wp_update_post( $updateEvent );

				update_post_meta( $existingEvent, 'evcal_subtitle', $evcal_subtitle );
				update_post_meta( $existingEvent, 'evcal_allday', $AllDayEvent );
				update_post_meta( $existingEvent, 'isams_event_id', $event_id );
				update_post_meta( $existingEvent, 'evcal_srow', $evcal_srow );
				update_post_meta( $existingEvent, 'evcal_erow', $evcal_erow );
				update_post_meta( $existingEvent, 'event_year', $evYear );
				update_post_meta( $existingEvent, 'event_location', $Location );
				wp_set_object_terms( $existingEvent, $terms[0]->term_id, 'event_type', true );

			} else {

				$post_id = wp_insert_post(array (
				   'post_type' => 'ajde_events',
				   'post_title' => $Description,
				   'post_content' => $Description,
				   'post_status' => 'publish',
				));

				update_post_meta( $post_id, 'evcal_subtitle', $evcal_subtitle );
				update_post_meta( $post_id, 'evcal_allday', $AllDayEvent );
				update_post_meta( $post_id, 'isams_event_id', $event_id );
				update_post_meta( $post_id, 'evcal_srow', $evcal_srow );
				update_post_meta( $post_id, 'evcal_erow', $evcal_erow );
				update_post_meta( $post_id, 'event_year', $evYear );
				update_post_meta( $post_id, 'event_location', $Location );
				wp_set_object_terms( $post_id, $terms[0]->term_id, 'event_type', true );
			}
		}
	endforeach;

}


register_deactivation_hook(__FILE__, 'kingsdoha_isams_deactiation');

function kingsdoha_isams_deactiation() {
	wp_clear_scheduled_hook('isams_daily_event');
}