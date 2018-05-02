<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://cybercom.com
 * @since      1.0.0
 *
 * @package    Sk_Bike_Booking
 * @subpackage Sk_Bike_Booking/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Sk_Bike_Booking
 * @subpackage Sk_Bike_Booking/includes
 * @author     Daniel Pihlström <daniel.pihlstrom@cybercom.com>
 */
class Sk_Bike_Booking_Deactivator {


	public static function deactivate() {
		self::remove_role();
	}

	/**
	 * Remove custom role and caps for bike booking.
	 *
	 * @author Daniel Pihlström <daniel.pihlstrom@cybercom.com>
	 *
	 */
	public static function remove_role(){

		// remove the custom role
		remove_role( 'bikebooking_manager' );

		$caps = Sk_Bike_Booking_Admin::get_caps();

		// remove custom caps from administrator
		$role = get_role( 'administrator');
		foreach ( $caps as $cap => $value ) {
			$role->remove_cap( $cap );
		}

		// remove custom caps from editor
		$role = get_role( 'editor');
		foreach ( $caps as $cap => $value ) {
			$role->remove_cap( $cap );
		}


	}

}
