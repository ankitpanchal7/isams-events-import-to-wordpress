<?php
/**
 * Generated by the WordPress Option Page generator
 */

class ISamsSettings {
	private $isams_settings_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'isams_settings_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'isams_settings_page_init' ) );
	}

	public function isams_settings_add_plugin_page() {
		add_menu_page(
			'iSams Events Settings', // page_title
			'iSams Events Settings', // menu_title
			'manage_options', // capability
			'isams-settings', // menu_slug
			array( $this, 'isams_settings_create_admin_page' ), // function
			'dashicons-admin-generic', // icon_url
			99 // position
		);

		add_submenu_page( 'isams-settings', 'Sync', 'Sync', 'manage_options', 'sync', array( $this, 'isams_events_category_sync' ) );

	}

	public function isams_events_category_sync() {
		global $wpdb;

		if( isset( $_POST['events_sync_submit'] )){
			$this->sync_events_method();
		}

		if( isset( $_POST['category_sync_submit'] )){
			$this->sync_categories_method();
		}
		?>
		<br />
		<div class="wrapper">
			<form action="" name="category_sync" method="post">
				<h1>Sync Categories</h1>
				<input type="submit" name="category_sync_submit" class="button button-primary" value="Sync Categories with iSams">
			</form>
		</div>
		<br /><br />

		<div class="wrapper">
			<form action="" name="events_sync" method="post">
				<h1>Sync Events</h1>
				<input type="submit" name="events_sync_submit" class="button button-primary" value="Sync Events with iSams">
			</form>
		</div>
		<?php
	}


	public function isams_settings_create_admin_page() {
		$this->isams_settings_options = get_option( 'isams_settings_option_name' ); ?>

		<div class="wrap">
			<h2>iSams Events Settings</h2>
			<p></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'isams_settings_option_group' );
					do_settings_sections( 'isams-settings-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function isams_settings_page_init() {
		register_setting(
			'isams_settings_option_group', // option_group
			'isams_settings_option_name', // option_name
			array( $this, 'isams_settings_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'isams_settings_setting_section', // id
			'Settings', // title
			array( $this, 'isams_settings_section_info' ), // callback
			'isams-settings-admin' // page
		);

		add_settings_field(
			'isams_api_key_0', // id
			'iSams API Key', // title
			array( $this, 'isams_api_key_0_callback' ), // callback
			'isams-settings-admin', // page
			'isams_settings_setting_section' // section
		);

		add_settings_field(
			'start_date_1', // id
			'Start Date', // title
			array( $this, 'start_date_1_callback' ), // callback
			'isams-settings-admin', // page
			'isams_settings_setting_section' // section
		);

		add_settings_field(
			'end_date_2', // id
			'End Date', // title
			array( $this, 'end_date_2_callback' ), // callback
			'isams-settings-admin', // page
			'isams_settings_setting_section' // section
		);

		add_settings_field(
			'api_url_3', // id
			'API URL', // title
			array( $this, 'api_url_3_callback' ), // callback
			'isams-settings-admin', // page
			'isams_settings_setting_section' // section
		);
	}

	public function isams_settings_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['isams_api_key_0'] ) ) {
			$sanitary_values['isams_api_key_0'] = sanitize_text_field( $input['isams_api_key_0'] );
		}

		if ( isset( $input['start_date_1'] ) ) {
			$sanitary_values['start_date_1'] = sanitize_text_field( $input['start_date_1'] );
		}

		if ( isset( $input['end_date_2'] ) ) {
			$sanitary_values['end_date_2'] = sanitize_text_field( $input['end_date_2'] );
		}

		if ( isset( $input['api_url_3'] ) ) {
			$sanitary_values['api_url_3'] = sanitize_text_field( $input['api_url_3'] );
		}

		return $sanitary_values;
	}

	public function isams_settings_section_info() {
		
	}

	public function isams_api_key_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="isams_settings_option_name[isams_api_key_0]" id="isams_api_key_0" value="%s">',
			isset( $this->isams_settings_options['isams_api_key_0'] ) ? esc_attr( $this->isams_settings_options['isams_api_key_0']) : ''
		);
	}

	public function start_date_1_callback() {
		printf(
			'<input class="regular-text" type="text" name="isams_settings_option_name[start_date_1]" id="start_date_1" value="%s" placeholder="2019-01-01">',
			isset( $this->isams_settings_options['start_date_1'] ) ? esc_attr( $this->isams_settings_options['start_date_1']) : ''
		);
	}

	public function end_date_2_callback() {
		printf(
			'<input class="regular-text" type="text" name="isams_settings_option_name[end_date_2]" id="end_date_2" value="%s" placeholder="2019-12-17">',
			isset( $this->isams_settings_options['end_date_2'] ) ? esc_attr( $this->isams_settings_options['end_date_2']) : ''
		);
	}

	public function api_url_3_callback() {
		printf(
			'<input class="regular-text" type="text" name="isams_settings_option_name[api_url_3]" id="api_url_3" value="%s">',
			isset( $this->isams_settings_options['api_url_3'] ) ? esc_attr( $this->isams_settings_options['api_url_3']) : ''
		);
	}

	public function sync_events_method() {
		global $wpdb;	

		$settings = get_option( 'isams_settings_option_name' );
		$startDate = $settings["start_date_1"];
		$endDate = $settings["end_date_2"];

		$apiURL = $settings["api_url_3"]."?"."apiKey=".$settings["isams_api_key_0"];
		$filters = '<Filters> \n  <CalendarManager> \n    <Events StartDate=\"'.$startDate.'\" EndDate=\"'.$startDate.'\" /> \n  </CalendarManager> \n</Filters>';
		
		$rowData = json_decode( $this->get_events_from_isams( $apiURL, $startDate, $endDate ) );

		$events = $rowData->iSAMS->CalendarManager->Events;
		// echo '<pre>';
		// print_r($events);
		// echo '</pre>';
		// exit;
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
			
			if( $term_id != 0 && $term_id != '' ){ 

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
				$evcal_subtitle = date('l, F jS,', $evcal_srow)." ".date("h:i:s a",$evcal_srow)." - ".date("h:i:s a",$evcal_erow);
				
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
					   'comment_status' => 'closed',   // if you prefer
					   'ping_status' => 'closed',      // if you prefer
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
		echo '<h1>Events Successfully Imported.....</h1>';
	}
	public function sync_categories_method() {
		global $wpdb;

		$settings = get_option( 'isams_settings_option_name' );
		$startDate = $settings["start_date_1"];
		$endDate = $settings["end_date_2"];

		$apiURL = $settings["api_url_3"]."?"."apiKey=".$settings["isams_api_key_0"];
		$filters = '<Filters> \n  <CalendarManager> \n    <Events StartDate=\"'.$startDate.'\" EndDate=\"'.$startDate.'\" /> \n  </CalendarManager> \n</Filters>';
		
		$rowData = json_decode( $this->get_categories_from_isams( $apiURL, $startDate, $endDate ) );
		$categories = $rowData->iSAMS->CalendarManager->Categories->Category;

		foreach( $categories as $category ) {

			// $term_isams_id = 0;
			foreach( $category as $ids ){ $term_isams_id = $ids; break; }	
			$slug = strtolower(str_replace(" ", "-", $category->Name));
			
			if( $term_isams_id != 0 && $term_isams_id != '' ) {
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
		echo '<h1>Categories Successfully Imported.....</h1>';
	}

	public function get_categories_from_isams( $apiURL, $startDate, $endDate ){

		$response = $this->curl_call( $apiURL, $startDate, $endDate );
		return $response;

	}

	public function get_events_from_isams( $apiURL, $startDate, $endDate ){

		$response = $this->curl_call( $apiURL, $startDate, $endDate );
		return $response;

	}

	public function curl_call( $apiURL, $startDate, $endDate ) {

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // Skip SSL Verification
		curl_setopt_array($curl, array(
		  CURLOPT_URL =>  $apiURL,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "<Filters> \n  <CalendarManager> \n    <Events StartDate=\"".$startDate."\" EndDate=\"".$endDate."\" /> \n  </CalendarManager> \n</Filters>",
		  CURLOPT_HTTPHEADER => array(
		    "Accept: */*",
		    "Cache-Control: no-cache",
		    "Connection: keep-alive",
		    "Content-Type: application/json",
		    "Host: kingscollegedoha.isams.cloud",
		    "accept-encoding: gzip, deflate",
		    "cache-control: no-cache",
		    "content-length: 124",
		    "cookie: FeatureToggle_AuthenticationServer.iSAMS=True"
		  ),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return $err;
		} else {
		  return $response;
		}
	}



}
if ( is_admin() )
	$isams_settings = new ISamsSettings();