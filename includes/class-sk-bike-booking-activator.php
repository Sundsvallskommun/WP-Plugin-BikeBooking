<?php

/**
 * Fired during plugin activation
 *
 * @link       http://cybercom.com
 * @since      1.0.0
 *
 * @package    Sk_Bike_Booking
 * @subpackage Sk_Bike_Booking/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Sk_Bike_Booking
 * @subpackage Sk_Bike_Booking/includes
 * @author     Daniel Pihlström <daniel.pihlstrom@cybercom.com>
 */
class Sk_Bike_Booking_Activator {

	public static function activate() {
		self::add_role();
	}

	/**
	 * Add custom role and caps for bike booking.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 */
	public static function add_role(){


		$caps = Sk_Bike_Booking_Admin::get_caps();

		// add role and caps.
		add_role( 'bikebooking_manager', 'Hanterare av cykelbokningar', $caps );

		// adding cap to administrator.
		$role = get_role( 'administrator' );
		foreach ( $caps as $cap => $value ) {
			$role->add_cap( $cap );
		}

		// adding cap to editor.
		$role = get_role( 'editor' );
		foreach ( $caps as $cap => $value ) {
			$role->add_cap( $cap );
		}

	}

}
