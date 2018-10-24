<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://cybercom.com
 * @since      1.0.0
 *
 * @package    Sk_Bike_Booking
 * @subpackage Sk_Bike_Booking/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Sk_Bike_Booking
 * @subpackage Sk_Bike_Booking/includes
 * @author     Daniel Pihlström <daniel.pihlstrom@cybercom.com>
 */
class Sk_Bike_Booking {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Sk_Bike_Booking_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SK_BIKE_BOOKING_VERSION' ) ) {
			$this->version = SK_BIKE_BOOKING_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'sk-bike-booking';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Sk_Bike_Booking_Loader. Orchestrates the hooks of the plugin.
	 * - Sk_Bike_Booking_i18n. Defines internationalization functionality.
	 * - Sk_Bike_Booking_Admin. Defines all hooks for the admin area.
	 * - Sk_Bike_Booking_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sk-bike-booking-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sk-bike-booking-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sk-bike-booking-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-sk-bike-booking-public.php';

		$this->loader = new Sk_Bike_Booking_Loader();

	}

	/**
	 * Setting up acf-json sync for acf fields.
	 *
	 * @since 1.0.0
	 *
	 * @param $paths
	 *
	 * @return array
	 */
	public function acf_json( $paths ) {
		$paths []= plugin_dir_path( __FILE__ ) . 'acf-json';
		return $paths;
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Sk_Bike_Booking_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Sk_Bike_Booking_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Sk_Bike_Booking_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_admin, 'register_post_type', 10 );
		$this->loader->add_action( 'init', $plugin_admin, 'register_taxonomy', 10 );
		$this->loader->add_action( 'init', $plugin_admin, 'add_options_page', 10 );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'export_page', 10, 1 );

		$this->loader->add_filter( 'manage_bikebooking_posts_columns', $plugin_admin, 'custom_admin_columns' );
		$this->loader->add_action( 'manage_bikebooking_posts_custom_column', $plugin_admin, 'custom_admin_column', 10, 2 );

		$this->loader->add_action( 'save_post_bikebooking', $plugin_admin, 'save_post', 10, 1 );
		


	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Sk_Bike_Booking_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp', $plugin_public, 'add_shortcode' );
		$this->loader->add_action( 'wp', $plugin_public, 'book_request' );

		$this->loader->add_action( 'wp_ajax_nopriv_book_bike', $plugin_public, 'book_bike', 99 );
		$this->loader->add_action( 'wp_ajax_book_bike', $plugin_public, 'book_bike', 99 );


		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_filter( 'acf/settings/load_json', $this, 'acf_json' );

		$this->loader->add_filter( 'single_template', $plugin_public, 'single_template' );
		$this->loader->add_filter( 'archive_template', $plugin_public, 'archive_template' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Sk_Bike_Booking_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
