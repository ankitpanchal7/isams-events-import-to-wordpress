<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/ankitmaru
 * @since      1.0.0
 *
 * @package    Isams_Events_Sync_By_Waytocode
 * @subpackage Isams_Events_Sync_By_Waytocode/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Isams_Events_Sync_By_Waytocode
 * @subpackage Isams_Events_Sync_By_Waytocode/admin
 * @author     Ankit Panchal <ankitmaru@live.in>
 */
class Isams_Events_Sync_By_Waytocode_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Isams_Events_Sync_By_Waytocode_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Isams_Events_Sync_By_Waytocode_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/isams-events-sync-by-waytocode-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Isams_Events_Sync_By_Waytocode_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Isams_Events_Sync_By_Waytocode_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/isams-events-sync-by-waytocode-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Adding Image Field
	 * @return void 
	 */
	public function event_type_add_category_id( $term ) {
		?>
		<div class="form-field">
			<label for="isams_cat_id"><?php _e( 'iSams Category ID', 'isams' ); ?></label>

			<input type="text" name="isams_cat_id" id="isams_cat_id" value="">
		</div>
	<?php
	}

	/**
	 * Edit iSams Category Field
	 * @return void 
	 */
	public function event_type_edit_isams_cat_id( $term ) {
		
		// put the term ID into a variable
		$t_id = $term->term_id;
	 
		$isams_cat_id = get_term_meta( $t_id, 'isams_cat_id', true ); 
		?>
		<tr class="form-field">
			<th><label for="isams_cat_id"><?php _e( 'iSams Category ID', 'isams' ); ?></label></th>
			<td>	 
				<input readonly type="text" name="isams_cat_id" id="isams_cat_id" value="<?php echo esc_attr( $isams_cat_id ) ? esc_attr( $isams_cat_id ) : ''; ?>">
			</td>
		</tr>
	<?php
	}
	/**
	 * Saving Category ID
	 */
	public function event_type_isams_save_cat_id( $term_id ) {
		
		if ( isset( $_POST['isams_cat_id'] ) ) {
			$isams_cat_id = $_POST['isams_cat_id'];
			if( $isams_cat_id ) {
				 update_term_meta( $term_id, 'isams_cat_id', $isams_cat_id );
			}
		} 
	}  

}	




