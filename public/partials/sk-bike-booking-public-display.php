<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://cybercom.com
 * @since      1.0.0
 *
 * @package    Sk_Bike_Booking
 * @subpackage Sk_Bike_Booking/public/partials
 */

$bikes = Sk_Bike_Booking_Public::get_bikes();
?>

<div class="sk-collapse bikebooking-period">
	<h2>
		<a data-toggle="collapse" href="#period<?php echo $i; ?>" aria-expanded="false"
		   aria-controls="period<?php echo $i; ?>" class="collapsed">
			<?php printf( __( '%s st tillgängliga cyklar %s', 'bikebooking_textdomain' ), Sk_Bike_Booking_Public::sum_of_bikes_available( $period_start, $period_end ), 'v' . date( 'W', strtotime( $period_start ) ) . ' - v' . date( 'W', strtotime( $period_end ) ) );?>
			<span class="date-period">
				<?php printf( __( '%s till %s', 'bikebooking_textdomain' ), date_i18n( 'l j F Y', strtotime( $period_start ) ), date_i18n( 'l j F', strtotime( $period_end ) ) );?>
			</span>
		</a>
	</h2>
	<div class="collapse" id="period<?php echo $i; ?>" aria-expanded="false" style="/*height: 0px;*/">
		<div class="bike-period">
			<?php if ( Sk_Bike_Booking_Public::sum_of_bikes_available( $period_start, $period_end ) === 0 ): ?>
				<p><?php _e( 'Det finns inga tillgängliga cyklar för denna period.', 'bikebooking_textdomain' );?></p>
			<?php endif;?>
			<?php $j = 0;
foreach ( $bikes as $bike ): $j++;?>
					<?php if ( Sk_Bike_Booking_Public::is_bike_available( $bike->ID, $period_start, $period_end ) ): ?>
						<div class="bike">
							<div class="row bike-info">
								<div class="col-sm-3 bike__image">
									<?php echo get_the_post_thumbnail( $bike->ID, 'medium' ); ?>
								</div>
								<div class="col-sm-9">
									<h4><?php echo $bike->post_title; ?>
										<button type="button" class="btn btn-primary btn-sm" data-toggle="collapse"
										        data-target="#bike-<?php echo $bike->ID . $i; ?>" aria-expanded="false"
										        aria-controls="bike-<?php echo $bike->ID . $i; ?>"><?php _e( 'Boka', 'bikebooking_textdomain' );?>
										</button>
									</h4>
									<?php Sk_Bike_Booking_Public::the_attributes( $bike->ID );?>
								</div>
							</div>


							<div class="collapse bike-book-collapse" id="bike-<?php echo $bike->ID . $i; ?>">
								<div class="alert alert-warning">
									<h3><?php _e( 'Bokningsförfrågan', 'bikebooking_textdomain' );?></h3>
									<div class="alert alert-inner selected-items">
										<div class="row bike-info">
											<div class="col-sm-2 bike__image">
												<?php echo get_the_post_thumbnail( $bike->ID, 'medium' ); ?>
											</div>
											<div class="col-sm-10">
												<p><?php echo $bike->post_title; ?></p>
											</div>
										</div>
										<div class="selected-accessorie"></div>
									</div>
									<div class="row">
										<div class="col-sm-12">
											<h4><?php _e( 'Välj cykelvagn', 'bikebooking_textdomain' );?></h4>
										</div>
									</div>

									<?php
    $accessories = Sk_Bike_Booking_Public::get_accessories( $bike->ID, $period_start . ':' . $period_end );
    if ( !empty( $accessories ) ): ?>
										<?php foreach ( $accessories as $key => $accessorie ): ?>
											<div class="row accessorie-info<?php echo $key === 0 ? ' first' : null; ?>">
												<div class="col-xs-3 col-sm-2 bike__image">
													<?php if ( !empty( $accessorie->image ) ): ?>
														<img src="<?php echo $accessorie->image; ?>">
													<?php endif;?>
											</div>
											<div class="col-xs-6 col-sm-8">
												<p><?php echo $accessorie->name; ?></p>
												<p class="desc"><?php echo $accessorie->description; ?></p>
											</div>
											<div class="col-xs-3 col-sm-2">
												<button type="button"
												        data-accessorie="<?php echo $accessorie->term_id; ?>"
												        class="btn btn-primary btn-sm" aria-pressed="false"
												        autocomplete="off">
													<?php _e( 'Välj', 'bikebooking_textdomain' );?>
												</button>
											</div>
										</div>
									<?php endforeach;?>



								<?php else: ?>
									<div class="alert alert-inner">
										<p><?php _e( 'Det finns inga tillgängliga cykelvagnar för denna period eller för denna typ av cykel.', 'bikebooking_textdomain' );?></p>
									</div>
								<?php endif;?>

								<div class="form-info">
									<h4><?php _e( 'Formulär för bokningsförfrågan', 'bikebooking_textdomain' );?></h4>
									<p><?php _e( 'Observera att du kommer få ett e-postmeddelande där du behöver bekräfta din bokning. Bokningen är ej giltig förrän du erhållit ett bokningsnummer vilket skickas efter att du bekräftat din bokning.', 'bikebooking_textdomain' );?></p>
									<p><?php _e( 'Samtliga fält i formuläret är obligatoriska.', 'bikebooking_textdomain' );?></p>
								</div>
								<form>
									<div class="form-group row">
										<label for="booker-email-<?php echo $j . strtotime( $period_start ); ?>"
										       class="col-sm-3 col-form-label text-right"><?php _e( 'E-postadress', 'bikebooking_textdomain' );?></label>
										<div class="col-sm-9">
											<input type="email"
											       id="booker-email-<?php echo $j . strtotime( $period_start ); ?>"
											       name="booker_email" class="booker-email form-control">
										</div>
									</div>

									<div class="form-group row">
										<label for="booker-name-<?php echo $j . strtotime( $period_start ); ?>"
										       class="col-sm-3 col-form-label text-right"><?php _e( 'Namn', 'bikebooking_textdomain' );?></label>
										<div class="col-sm-9">
											<input type="text"
											       id="booker-name-<?php echo $j . strtotime( $period_start ); ?>"
											       name="booker_name" class="booker-name form-control">
										</div>
									</div>

									<div class="form-group row">
										<label for="booker-phone-<?php echo $j . strtotime( $period_start ); ?>"
										       class="col-sm-3 col-form-label text-right"><?php _e( 'Telefonnummer', 'bikebooking_textdomain' );?></label>
										<div class="col-sm-9">
											<input type="text"
											       id="booker-phone-<?php echo $j . strtotime( $period_start ); ?>"
											       name="booker_phone" class="booker-phone form-control">
										</div>
									</div>
									<div class="form-group row">

										<div class="col-sm-3"></div>
										<div class="form-check col-sm-9">
										<?php if ( get_field( 'bb_text_compliance', 'options' ) ): ?>
												<p class="booker-compliance-text"><?php echo get_field( 'bb_text_compliance', 'options' ); ?></p>
											<?php else: ?>
												<p class="booker-compliance-text"><?php _e( 'För att kunna erbjuda denna bokningsfunktion behöver vi lagra de uppgifter som du själv skriver in (e-postadress, namn och telefonnummer). Vi behöver därför ditt samtycke för att vi sparar den informationen.', 'bikebooking_textdomain' );?></p>
											<?php endif;?>
										<input class="booker-compliance form-check-input" type="checkbox" name="booker_compliance" id="booker-compliance-text-<?php echo $j . strtotime( $period_start ); ?>" value="1">
										<label class="form-check-label" for="booker-compliance-text-<?php echo $j . strtotime( $period_start ); ?>"><?php _e( 'Ja, jag godkänner ovanstående.', 'bikebooking_textdomain' );?></label>
										</div>

									</div>

									<div class="form-group row text-right">
										<div class="col-sm-12">
											<button type="button" class="btn btn-primary btn-sm btn-close"><?php _e( 'Stäng', 'bikebooking_textdomain' );?>
											</button>
											<button type="button" data-bike="<?php echo $bike->ID; ?>"
											        data-period="<?php echo $period_start . ':' . $period_end; ?>"
											        class="book-a-bike btn btn-primary btn-sm"><?php _e( 'Skicka förfrågan', 'bikebooking_textdomain' );?></button>
										</div>
									</div>

								</form>

							</div><!-- .alert -->
						</div><!-- .bike-book-collapse -->

					</div><!-- .bike -->
				<?php endif;?>
			<?php endforeach;?>
		</div><!-- .bike-period -->
	</div>
</div>


