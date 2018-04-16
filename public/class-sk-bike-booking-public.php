<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://cybercom.com
 * @since      1.0.0
 *
 * @package    Sk_Bike_Booking
 * @subpackage Sk_Bike_Booking/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Sk_Bike_Booking
 * @subpackage Sk_Bike_Booking/public
 * @author     Daniel Pihlström <daniel.pihlstrom@cybercom.com>
 */
class Sk_Bike_Booking_Public {

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

	private $email_headers;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->email_headers = array(
			'From: Cykelbokning - sundsvall.se <webbgruppen@sundsvall.se>',
			'Content-Type:text/html;charset=UTF-8'
		);
	}


	public function book_request() {
		if ( isset( $_GET['bikebooking'] ) && $_GET['bikebooking'] === 'confirm' ) {
			if(isset($_GET['ref'])){
				$this->book_confirm( $_GET['ref'] );
			}
		}

		if ( isset( $_GET['bikebooking'] ) && $_GET['bikebooking'] === 'cancel' ) {
			if(isset($_GET['ref'])){
				$this->book_cancel( $_GET['ref'] );
			}
		}

	}

	public static function requests() {
		if ( ! is_post_type_archive( 'bikebooking' ) ){
			return false;
		}
		?>
			<?php if ( isset( $_GET['status'] ) && $_GET['status'] === 'confirmed' ) : ?>
				<div class="bikebooking-status">
					<h1 class="single-post__title">Din bokning är bekräftad</h1>
					<?php if ( isset( $_GET['accessorie'] ) && $_GET['accessorie'] === 'removed' ) : ?>
						<p><?php _e('Tyvärr kan vi inte erbjuda den efterfrågade cykelvagnen då den redan har blivit bokad.', 'bikebooking_textdomain') ;?></p>
					<?php endif;?>
					<p><?php _e('Vi har skickat mer information om din bokning till din e-postadress.', 'bikebooking_textdomain') ;?></p>
				</div>
			<?php endif;?>

			<?php if ( isset( $_GET['status'] ) && $_GET['status'] === 'canceled' ) : ?>
				<div class="bikebooking-status">
					<h1 class="single-post__title"><?php _e('Din bokning är borttagen', 'bikebooking_textdomain') ;?></h1>
					<p><?php _e('En bekräftelse på din avbokning är skickad till din e-postadress.', 'bikebooking_textdomain') ;?></p>
				</div>
			<?php endif;?>

		<?php
	}

	public function book_cancel( $hash ){
		global $wpdb;
		echo $hash;

		$booking_id = $wpdb->get_var( $wpdb->prepare( "
			SELECT DISTINCT post_id FROM $wpdb->postmeta WHERE meta_key= 'bb-hash' AND meta_value = %s;
			", $hash ) );

		if(empty($booking_id)){
			error_log('Bike booking: cannot find post_id from given hash when cancel a booking i requested.');
			return false;
		}

		update_post_meta( $booking_id, 'bb-canceled', 'canceled_by_user:' .date_i18n('Y-m-d H:i:s') );
		wp_trash_post( $booking_id );

		$this->send_booking_email_canceled( $booking_id );
		wp_redirect( get_post_type_archive_link('bikebooking') . '?status=canceled' );
		exit();

	}


	public function book_confirm( $hash ){

		$transient = get_transient( 'bikebooking_' . $hash );

		if( empty( $transient ) ){
			error_log('Bike booking: transient is missing or not valid');
			return false;
		}

		$this->book_insert( $transient );


	}

	public function book_insert( $transient ){

		$booking = explode( ':', base64_decode( $transient ) );

		//Util::debug( $booking );
		//die();


		// check if the bike i still available
		if ( ! self::is_bike_available( $booking[1], $booking[2], $booking[3] ) ) {
			wp_redirect( get_post_type_archive_link('bikebooking') . '?status=bike-unavailable' );
			exit();
		}

		$accessorie_removed = false;
		if( !empty( $booking[4] ) ){
			if ( ! self::is_accessorie_available( $booking[4], $booking[2], $booking[3] ) ) {
				$accessorie_removed = true;
				$booking[4] = '';
			}

		}


		$args = array(
			'post_type'  => 'bikebooking',
			'meta_key'   => 'bb-hash',
			'meta_value' => $transient,
		);

		$posts = get_posts( $args );

		if ( ! empty( $posts ) ) {
			error_log('Bike booking: booking already exists');
			return false;
		}

		$post_data = array(
			'post_author'   => '1',
			'post_title'    => $booking[0],
			'post_status'   => 'publish',
			'post_type'     => 'bikebooking',
		);

		$post_id = wp_insert_post( $post_data );

		// set up content to be same as email body.
		$booking_cancel_url = get_bloginfo('url') . '/?bikebooking=cancel&ref=' . $transient;
		$content = 'Här kommer en bekräftelse på din bokade cykel.<br><br>';
		$content .= 'Bokningsreferens: ' . $post_id . '<br>';
		$content .= 'Cykel: ' . get_the_title( $booking[1] ) . '<br>';
		if( !empty( $booking[4] ) ){
			$term = get_term( $booking[4] );
			$content .= 'Tillbehör: ' . $term->name . '<br>';
		}
		if( $accessorie_removed === true ){
			$content .= 'Tillbehör: Cykelvagn borttagen.<br>';
			$content .= 'Tyvärr har cykelvagnen redan blivit bokad. Är det viktigt med cykelvagn ber vi dig avboka denna bokning och försöka finna kombinationen av cykel och cykelvagn i en annan period.<br>';
		}
		$content .= 'Låneperiod: ' . $booking[2] . ':' . $booking[3] . '<br><br>';
		$content .= sprintf( __( '<a href="%s">Klicka här för att avboka din elcykel</a>.', 'bikebooking_textdomain' ), $booking_cancel_url);



		wp_update_post( array(
				'ID'         => $post_id,
				'post_title' => $post_id . ' | ' . $booking[0],
				'post_name' => $post_id,
				'post_content' => $content
			)
		);

		$post_meta = array(
			'bb-email'         => $booking[0],
			'bb-bike-id'       => $booking[1],
			'bb-period'        => $booking[2] . ':' . $booking[3],
			'bb-accessorie-id' => $booking[4],
			'bb-name'          => $booking[5],
			'bb-phone'         => $booking[6],
			'bb-hash'          => $transient
		);

		foreach ( $post_meta as $meta_key => $meta_value ) {
			update_post_meta( $post_id, $meta_key, $meta_value );
		}

		delete_transient( 'bikebooking_' . $transient );


		$this->send_booking_email_confirmed( $post_id );

		if( $accessorie_removed === true ){
			$accessorie_removed = '&accessorie=removed';
		}

		wp_redirect( get_post_type_archive_link('bikebooking') . '?status=confirmed' . $accessorie_removed );
		exit();


	}


	/**
	 * Register the short code.
	 *
	 * @since 1.0.0
	 *
	 */
	public function add_shortcode() {
		add_shortcode( 'bike-booking', array( $this, 'output' ) );
	}


	/**
	 * Render the html.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @return string
	 */
	public function output(){
		//start buffering
		ob_start();

		$startdate = '2018-03-19';
		//$startdate = '2018-03-27';

		$period_start = 'even';
		if( date('W', strtotime( $startdate ))%2 ){
			$period_start = 'odd';
		}


		if( date('W')%2 && $period_start === 'odd'){
			$start    = new DateTime();
		}else{
			$start    = new DateTime('-1 week');
		}

		$end      = new DateTime('+ 10 weeks');
		$interval = new DateInterval('P2W');
		//$interval = new DateInterval('P10D');
		$period   = new DatePeriod($start, $interval, $end);
		$i = 0;

		//echo $start;

		foreach ( $period as $date ) : $i ++;
			$period = $this->get_start_of_week_date( $date->format('Y-m-d') );
			$period_start = $period->format('Y-m-d');
			$period_end = $period->modify('+11 days')->format('Y-m-d');
			require('partials/sk-bike-booking-public-display.php');
			endforeach;

		$output = ob_get_contents();
		ob_get_clean();

		return $output;
	}


	public function get_start_of_week_date($date = null) {
		if ($date instanceof DateTime) {
			$date = clone $date;
		} else if (!$date) {
			$date = new DateTime();
		} else {
			$date = new DateTime($date);
		}

		$date->setTime(0, 0, 0);

		if ($date->format('N') == 1) {
			// If the date is already a Monday, return it as-is
			return $date;
		} else {
			// Otherwise, return the date of the nearest Monday in the past
			// This includes Sunday in the previous week instead of it being the start of a new week
			return $date->modify('last monday');
		}
	}


	/**
	 * get_bikes
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @return array
	 */
	public static function get_bikes(){
		$args = array(
			'post_type' => 'bike',
			'post_status' => 'publish',
			'posts_per_page' => -1
		);

		$bikes = get_posts( $args );

		return $bikes;

	}


	/**
	 * get_bikes
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @return array
	 */
	public static function get_accessories( $period = false ){

		$period = explode(':', $period );

		$terms = get_terms( array(
			'taxonomy'   => 'bike-accessories',
			'hide_empty' => false,
		) );

		//Util::debug( $terms );

		foreach ( $terms as $key => $term ) {

			if( !self::is_accessorie_available( $term->term_id, $period[0], $period[1])){
				unset($terms[$key]);
			}else{
				$image = get_field('bb-accessorie-image', $term->taxonomy . '_' . $term->term_id) ;
				if(!empty( $image )){
					$terms[$key]->image = $image['sizes']['medium'];
				}else{
					$terms[$key]->image = '';
				}
			}
		}

		//Util::debug( $terms );
		
		return $terms;

	}


	/**
	 * get_attributes
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $post_id
	 */
	public static function the_attributes( $post_id ) {
		$taxonomy = 'bike-attributes';
		$terms = wp_get_post_terms( $post_id, $taxonomy );

		end( $terms );
		$last = key( $terms );

		if(!empty( $terms )) : ?>
			<span>Egenskaper: </span>
			<?php foreach ($terms as $key => $term ) : ?>
				<?php echo $last !== $key ? $term->name. ',' :  $term->name; ?>
			<?php endforeach; ?>
		<?php endif;


	}

	public function book_bike(){
		$booker['email'] = $_POST['booker_email'];
		$booker['name']  = $_POST['booker_name'];
		$booker['phone'] = $_POST['booker_phone'];
		$bike_id         = intval( $_POST['bike_id'] );
		$bike_period     = $_POST['bike_period'];
		$accessorie_id   = isset( $_POST['accessorie_id'] ) ? $_POST['accessorie_id'] : '';

		$response['error'] = false;

		if( empty( $booker['email'] ) || empty( $booker['name'] ) || empty( $booker['phone'] ) ){
			$response['error'] = 'Samtliga fält är obligatoriska';
			return wp_send_json( $response );

		} elseif ( !is_email( $booker['email'] ) ) {
			$response['error'] = 'Kontrollera din e-postadress.';
			return wp_send_json( $response );
		}else{
			$this->send_booking_email( $booker, $bike_id, $accessorie_id, $bike_period );
		}

		die();
	}


	/**
	 * sum_of_bikes_available
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $period_start
	 * @param $period_end
	 *
	 * @return bool
	 */
	public static function sum_of_bikes_available( $period_start, $period_end ){
		global $wpdb;

		$period = $period_start . ':' . $period_end;

		$bikes = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'bike' AND post_status = 'publish'");

		$booked = $wpdb->get_var( $wpdb->prepare( "
			SELECT COUNT(*) FROM $wpdb->posts as posts
		 		LEFT JOIN wp_postmeta as meta ON ( meta.post_id = posts.ID )
		 		WHERE 1=1 
		 		AND posts.post_type = 'bikebooking' AND posts.post_status = 'publish'
		 		AND meta.meta_key = 'bb-period' AND meta.meta_value = '%s';
		", $period ) );

		return ($bikes - $booked);

	}


	/**
	 * Check if a bike is available for a given period.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $bike_id
	 * @param $period_start
	 *
	 * @return bool
	 */
	public static function is_bike_available( $bike_id, $period_start, $period_end ){
		global $wpdb;

		$period = $period_start . ':' . $period_end;

		$result = $wpdb->get_var( $wpdb->prepare( "
			SELECT posts.ID FROM $wpdb->posts as posts
				LEFT JOIN wp_postmeta as meta1 ON ( posts.ID = meta1.post_id )
		 		LEFT JOIN wp_postmeta as meta2 ON ( posts.ID = meta2.post_id )
		 		WHERE 1=1 
		 		AND posts.post_type = 'bikebooking' AND posts.post_status = 'publish'
		 		AND meta1.meta_key = 'bb-bike-id' AND meta1.meta_value = '%s'
		 		AND meta2.meta_key = 'bb-period' AND meta2.meta_value = '%s'
		 	GROUP BY posts.ID;
		", $bike_id, $period ) );

		if ( $result ) {
			return false;
		}

		return true;

	}

	/**
	 * Check if a accessorie is available for a given period.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $bike_id
	 * @param $period_start
	 *
	 * @return bool
	 */
	public static function is_accessorie_available( $accessorie_id, $period_start, $period_end ){
		global $wpdb;

		$period = $period_start . ':' . $period_end;

		$result = $wpdb->get_var( $wpdb->prepare( "
			SELECT COUNT(posts.ID) FROM $wpdb->posts as posts
				LEFT JOIN wp_postmeta as meta1 ON ( posts.ID = meta1.post_id )
		 		LEFT JOIN wp_postmeta as meta2 ON ( posts.ID = meta2.post_id )
		 		WHERE 1=1 
		 		AND posts.post_type = 'bikebooking' AND posts.post_status = 'publish'
		 		AND meta1.meta_key = 'bb-accessorie-id' AND meta1.meta_value = '%s'
		 		AND meta2.meta_key = 'bb-period' AND meta2.meta_value = '%s'
		 	GROUP BY posts.ID;
		", $accessorie_id, $period ) );

		if( $result === null ){
			$result = 0;
		}

		$quantity = get_field('bb-accessories-quantity', 'bike-accessories_' . $accessorie_id );

		if ( $result >= $quantity ){
			return false;
		}

		return true;

	}





	public function send_booking_email( $booker, $bike_id, $accessorie_id = '', $bike_period ){


		// save transient
		$hash = base64_encode( $booker['email'] . ':' . $bike_id . ':' . $bike_period . ':' . $accessorie_id . ':' . $booker['name'] . ':' . $booker['phone'] . ':' . time() );
		set_transient( 'bikebooking_' . $hash, $hash, 60 * 60 );

		$booking_url = get_bloginfo('url') . '/?bikebooking=confirm&ref=' . $hash;

		// Build email.
		$subject = 'Bokningsförfrågan av elcykel';

		$body    = 'Tack för din bokningsförfrågan. <br><br>';
		$body    .= 'För att bekräfta din bokning behöver du klicka på den bifogade länken och följa instruktionerna. Vi vill förtydliga att bokningen inte är reserverad och ej heller giltig förrän du erhållit ett bokningsnummer.<br><br>';
		$body    .= sprintf( __( '<a href="%s">Klicka här för att bekräfta din bokning</a>', 'bikebooking_textdomain' ), $booking_url );

		$headers = implode( "\r\n", $this->email_headers );


		// Send it.
		wp_mail( $booker['email'], $subject, $body, $headers );


	}


	public function send_booking_email_confirmed( $post_id ){

		$booking_post = get_post( $post_id );

		$email = get_post_meta( $post_id, 'bb-email', true );
		$hash = get_post_meta( $post_id, 'bb-hash', true );
		$period = get_post_meta( $post_id, 'bb-period', true );

		$booking_cancel_url = get_bloginfo('url') . '/?bikebooking=cancel&ref=' . $hash;

		// Build email.
		$subject = 'Bokningsbekräftelse av elcykel';
		$body    = $booking_post->post_content;
		$headers = implode( "\r\n", $this->email_headers );

		// Send it.
		wp_mail( $email, $subject, $body, $headers );

	}


	public function send_booking_email_canceled( $booking_id ){

		$email = get_post_meta( $booking_id, 'bb-email', true );

		// Build email.
		$subject = 'Bekräftelse på avbokning';
		$body    = sprintf( __( 'En avbokning av elcykel med bokningsreferens %s är nu genomförd.<br><br>', 'bikebooking_textdomain' ), $booking_id );
		$headers = implode( "\r\n", $this->email_headers );

		// Send it.
		wp_mail( $email, $subject, $body, $headers );

	}


	/**
	 * Adding single template for post type.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $single_template
	 *
	 * @return string
	 */

	public function single_template( $single_template ) {

		// check for post type
		if ( is_singular( 'bikebooking' ) ) {
			$single_template = plugin_dir_path( __DIR__ ) . 'templates/single-bikebooking.php';
		}

		return $single_template;

	}


	/**
	 * Adding archive template.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 * @param $archive_template
	 *
	 * @return string
	 */

	public function archive_template( $archive_template ) {
		if ( is_post_type_archive( 'bikebooking' ) ) {
			$archive_template = plugin_dir_path( __DIR__ ) . 'templates/request-bikebooking.php';
		}

		return $archive_template;
	}


	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sk-bike-booking-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sk-bike-booking-public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->plugin_name, 'ajax_object', array(
				'ajaxurl'    => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'ajax_nonce' )
			)
		); // setting ajaxurl and nonce

	}

}
