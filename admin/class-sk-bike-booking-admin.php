<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://cybercom.com
 * @since      1.0.0
 *
 * @package    Sk_Bike_Booking
 * @subpackage Sk_Bike_Booking/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sk_Bike_Booking
 * @subpackage Sk_Bike_Booking/admin
 * @author     Daniel Pihlström <daniel.pihlstrom@cybercom.com>
 */
class Sk_Bike_Booking_Admin {

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
	 * Register post types.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 */
	public function register_post_type() {

		register_post_type( 'bikebooking',
			array(
				'labels'               => array(
					'name'          => __( 'Bokningar', 'sk_tivoli' ),
					'singular_name' => __( 'Bokning', 'sk_tivoli' ),
					'add_new'       => __( 'Ny bokning', 'sk_tivoli' ),
					'add_new_item'  => __( 'Skapa ny bokning', 'sk_tivoli' ),
					'edit_item'     => __( 'Redigera bokning', 'sk_tivoli' ),
				),
				'public'               => true,
				'show_ui'              => true,
				'menu_position'        => 6,
				'menu_icon'            => 'dashicons-list-view',
				'has_archive'          => true,
				'hierarchical'         => false,
				'rewrite'              => array( 'slug' => 'cykelbokningar' ),
				'supports'             => array( 'title' ),
				'show_in_menu'         => 'edit.php?post_type=bike',
				'register_meta_box_cb' => array( $this, 'booking_meta_box' ),
				'capability_type' => array('post','bikebooking', 'bikebookings'),
				'capabilities' => array(
					'edit_post'          => 'edit_bikebooking',
					'edit_posts'         => 'edit_bikebookings',
					'edit_others_posts'  => 'edit_others_bikebookings',
					//'publish_posts'      => 'publish_bikebookings',
					'read_post'          => 'read_bikebooking',
					'read_private_posts' => 'read_private_bikebooking',
					'delete_post'        => 'delete_bikebooking',
					'delete_posts'       => 'delete_bikebooking'
				),


			)
		);



		register_post_type( 'bike',
			array(
				'labels'          => array(
					'name'          => __( 'Cyklar', 'sk_tivoli' ),
					'singular_name' => __( 'Cykel', 'sk_tivoli' ),
					'menu_name'     => __( 'Elcyklar', 'sk_tivoli' ),
					'add_new'       => __( 'Ny cykel', 'sk_tivoli' ),
					'add_new_item'  => __( 'Skapa ny cykel', 'sk_tivoli' ),
					'edit_item'     => __( 'Redigera cykel', 'sk_tivoli' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'menu_position'   => 6,
				'menu_icon'       => 'dashicons-list-view',
				'has_archive'     => true,
				'hierarchical'    => false,
				'supports'        => array( 'title', 'thumbnail' ),
				'capability_type' => array('post','bike', 'bikes'),
				'capabilities' => array(
					'edit_post'          => 'edit_bike',
					'edit_posts'         => 'edit_bikes',
					'edit_others_posts'  => 'edit_others_bikes',
					'publish_posts'      => 'publish_bikes',
					'read_post'          => 'read_bike',
					'read_private_posts' => 'read_private_bike',
					'delete_post'        => 'delete_bike',
					'delete_posts'       => 'delete_bike'
				),
			)
		);

	}

	/**
	 * Setting the caps for bike booking.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @return array
	 */
	public static function get_caps(){

		$cpt_bikebooking = array(
			'read_bikebooking'              => true,
			'read_private_bikebookings'     => true,
			'edit_bikebooking'              => true,
			'edit_bikebookings'             => true,
			'edit_others_bikebookings'      => true,
			'edit_published_bikebookings'   => true,
			'publish_bikebookings'          => true,
			'delete_bikebooking'            => true,
			'delete_bikebookings'           => true,
			'delete_private_bikebookings'   => true,
			'delete_published_bikebookings' => true,
			'delete_others_bikebookings'    => true,
		);

		$cpt_bike             = array(
			'read_bike'              => true,
			'read_private_bikes'     => true,
			'edit_bike'              => true,
			'edit_bikes'             => true,
			'edit_others_bikes'      => true,
			'edit_published_bikes'   => true,
			'publish_bikes'          => true,
			'delete_bike'            => true,
			'delete_bikes'           => true,
			'delete_private_bikes'   => true,
			'delete_published_bikes' => true,
			'delete_others_bikes'    => true,
		);
		$tax_bike_attributes  = array(
			'assign_bike-attributes' => true,
			'edit_bike-attributes'   => true,
			'manage_bike-attributes' => true,
			'delete_bike-attributes' => true,
		);
		$tax_bike_accessories = array(
			'assign_bike-accessories' => true,
			'edit_bike-accessories'   => true,
			'manage_bike-accessories' => true,
			'delete_bike-accessories' => true,
		);

		$misc = array(
			'read'         => true,
			'upload_files' => true
		);

		$caps = array_merge( $cpt_bike, $cpt_bikebooking, $tax_bike_accessories, $tax_bike_attributes, $misc );

		return $caps;

	}

	/**
	 * Adding meta boxes.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $post
	 */
	function booking_meta_box( $post ) {
		add_meta_box(
			'booking-info',
			__( 'Bokningsbekräftelse', 'bikebooking_textdomain' ),
			array( $this, 'booking_confirm_meta_box_output')
		);

		add_meta_box(
			'booking-meta',
			__( 'Uppgifter om låntagare', 'bikebooking_textdomain' ),
			array( $this, 'booking_data_meta_box_output')
		);

	}


	/**
	 * Print text in meta box.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $post
	 */
	function booking_confirm_meta_box_output( $post ){
		echo '<div>'.$post->post_content.'</div>';
	}

	/**
	 * Print text in meta box.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $post
	 */
	function booking_data_meta_box_output( $post ){
		?>
		<div>
			<p><span>Namn:</span> <?php echo get_post_meta( $post->ID, 'bb-name', true);?></p>
			<p><span>Telefonnummer:</span> <?php echo get_post_meta( $post->ID, 'bb-phone', true);?></p>
			<p><span>E-postadress:</span> <?php echo get_post_meta( $post->ID, 'bb-email', true);?></p>
		</div>
	<?php
	}


	/**
	 * Adding custom column to wp admin list.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $columns
	 *
	 * @return array
	 */
	function custom_admin_columns( $columns ) {
		$custom_columns = array();
		foreach ( $columns as $key => $column ) {
			switch ( $key ) {
				case 'date' :
					unset( $columns['date'] );
					$custom_columns['bb_period'] = __( 'Period', 'bikebooking_textdomain' );
					//$custom_columns['end_date'] = __( 'Tas ned', 'digitalboard_textdomain' );

					break;
			}

			$custom_columns[ $key ] = $column;

		}

		return $custom_columns;
	}

	/**
	 * Populate a custom value to wp admin list.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $column
	 * @param $post_id
	 */
	function custom_admin_column( $column, $post_id ) {

		switch ( $column ) {

			case 'bb_period' :
				$bb_period = get_post_meta( $post_id, 'bb-period', true );
				echo !empty( $bb_period ) ? $bb_period : null;
				break;
/*
			case 'end_date' :
				$type = get_field( 'digitalboard_date_down', $post_id);
				echo !empty( $type ) ? $type : null;
				break;
*/
		}
	}




	/**
	 * Register taxonomies for place and species.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 */
	public function register_taxonomy() {

		register_taxonomy(
			'bike-attributes',
			'bike',
			array(
				'label'        => __( 'Egenskaper', 'sk_tivoli' ),
				'public'       => true,
				'show_ui'      => true,
				'hierarchical' => true,
				'capabilities' => array(
					'assign_terms' => 'assign_bike-attributes',
					'edit_terms'   => 'edit_bike-attributes',
					'manage_terms' => 'manage_bike-attributes',
					'delete_terms' => 'delete_bike-attributes',
				)
			)
		);

		register_taxonomy(
			'bike-accessories',
			'bike',
			array(
				'label'        => __( 'Cykelvagnar', 'sk_tivoli' ),
				'public'       => true,
				'show_ui'      => true,
				'hierarchical' => true,
				'capabilities' => array(
					'assign_terms' => 'assign_bike-accessories',
					'edit_terms'   => 'edit_bike-accessories',
					'manage_terms' => 'manage_bike-accessories',
					'delete_terms' => 'delete_bike-accessories',
				)
			)
		);
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
		 * defined in Sk_Bike_Booking_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sk_Bike_Booking_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sk-bike-booking-admin.css', array(), $this->version, 'all' );

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
		 * defined in Sk_Bike_Booking_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sk_Bike_Booking_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sk-bike-booking-admin.js', array( 'jquery' ), $this->version, false );

	}

}
