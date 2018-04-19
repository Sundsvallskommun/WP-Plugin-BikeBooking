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
		<a data-toggle="collapse" href="#temp<?php echo $i;?>" aria-expanded="false" aria-controls="temp<?php echo $i;?>" class="collapsed">
			<?php echo Sk_Bike_Booking_Public::sum_of_bikes_available( $period_start, $period_end ); ?> st Tillgängliga cyklar <?php echo 'v'.date('W', strtotime( $period_start ) ).' - v'.date('W', strtotime( $period_end ) ) ;?>
			<!--<?php echo Sk_Bike_Booking_Public::sum_of_bikes_available( $period_start, $period_end ); ?> st Tillgängliga cyklar <?php echo '(v'.date('W', strtotime( $period_start ) ).') '.$period_start. ' - (v'.date('W', strtotime( $period_end ) ).') '. $period_end;?>-->
		</a>
	</h2>
	<div class="collapse" id="temp<?php echo $i;?>" aria-expanded="false" style="/*height: 0px;*/">
		<div class="bike-period">
			<?php if( Sk_Bike_Booking_Public::sum_of_bikes_available( $period_start, $period_end ) === 0 ) : ?>
				<p><?php _e( 'Det finns inga tillgängliga cyklar för denna period.', 'bikebooking_textdomain' ); ?></p>
			<?php endif; ?>
		<?php $j=0; foreach ( $bikes as $bike ) : $j++;?>
			<?php if( Sk_Bike_Booking_Public::is_bike_available( $bike->ID, $period_start, $period_end ) ) : ?>
			<div class="bike">
				<div class="row bike-info">
					<div class="col-sm-3 bike__image">
						<?php echo get_the_post_thumbnail( $bike->ID, 'medium' ); ?>
					</div>
					<div class="col-sm-9">
						<h4><?php echo $bike->post_title; ?><button type="button" class="btn btn-primary btn-sm" data-toggle="collapse" data-target="#bike-<?php echo $bike->ID . $i; ?>" aria-expanded="false" aria-controls="bike-<?php echo $bike->ID . $i; ?>">Boka</button></h4>
						<?php Sk_Bike_Booking_Public::the_attributes( $bike->ID ); ?>
					</div>
				</div>



				<div class="collapse bike-book-collapse" id="bike-<?php echo $bike->ID . $i; ?>">
					<div class="alert alert-warning">
						<h3>Bokningsförfrågan</h3>
						<div class="alert alert-inner">
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

						<b>Välj cykelvagn</b>
						<?php
							$accessories = Sk_Bike_Booking_Public::get_accessories( $bike->ID, $period_start . ':' . $period_end );
							if ( ! empty( $accessories ) ) : ?>
								<?php foreach ( $accessories as $accessorie ) : ?>
									<div class="row accessorie-info">
										<div class="col-xs-3 col-sm-2 bike__image">
											<?php if(!empty($accessorie->image)) :?>
												<img src="<?php echo $accessorie->image; ?>">
											<?php endif; ?>
										</div>
										<div class="col-xs-6 col-sm-8">
											<p><?php echo $accessorie->name; ?></p>
											<p class="desc"><?php echo $accessorie->description; ?></p>
										</div>
										<div class="col-xs-3 col-sm-2">
											<button type="button" data-accessorie="<?php echo $accessorie->term_id; ?>" class="btn btn-primary btn-sm" aria-pressed="false" autocomplete="off">
												Välj
											</button>
										</div>
									</div>
								<?php endforeach; ?>
							<?php else: ?>
								<div class="alert alert-inner"><p><?php _e( 'Det finns inga tillgängliga cykelvagnar för denna period eller för denna typ av cykel.', 'bikebooking_textdomain' ); ?></p></div>
							<?php endif; ?>

						<div class="form-info">
							<p><?php _e( 'Observera att du kommer få ett e-postmeddelande där du behöver bekräfta din bokning. Bokningen är inte giltig förrän du erhållit ett bokningsnummer vilket skickas efter att du bekräftat din bokning.', 'bikebooking_textdomain');?></p>
							<p><?php _e( 'Samtliga fält är obligatoriska i nedan bokningsformulär.', 'bikebooking_textdomain');?></p>
						</div>

						<form>
							<div class="form-group row">
								<label for="booker-email-<?php echo $j . strtotime( $period_start ); ?>" class="col-sm-3 col-form-label text-right"><?php _e('E-postadress', 'bikebooking_textdomain');?></label>
								<div class="col-sm-9">
									<input type="email" id="booker-email-<?php echo $j . strtotime( $period_start ); ?>" name="booker_email" class="booker-email form-control">
								</div>
							</div>

							<div class="form-group row">
								<label for="booker-name-<?php echo $j . strtotime( $period_start ); ?>" class="col-sm-3 col-form-label text-right"><?php _e('Namn', 'bikebooking_textdomain');?></label>
								<div class="col-sm-9">
									<input type="text" id="booker-name-<?php echo $j . strtotime( $period_start ); ?>" name="booker_name" class="booker-name form-control">
								</div>
							</div>

							<div class="form-group row">
								<label for="booker-phone-<?php echo $j . strtotime( $period_start ); ?>" class="col-sm-3 col-form-label text-right"><?php _e('Telefonnummer', 'bikebooking_textdomain');?></label>
								<div class="col-sm-9">
									<input type="text" id="booker-phone-<?php echo $j . strtotime( $period_start ); ?>" name="booker_phone" class="booker-phone form-control">
								</div>
							</div>

							<div class="form-group row text-right">
								<div class="col-sm-12">
									<button type="button" class="btn btn-primary btn-sm btn-close">Stäng</button>
									<button type="button" data-bike="<?php echo $bike->ID;?>" data-period="<?php echo $period_start . ':' . $period_end; ?>" class="book-a-bike btn btn-primary btn-sm">Skicka förfrågan</button>
								</div>
							</div>
						</form>

					</div><!-- .alert -->
				</div><!-- .bike-book-collapse -->

			</div><!-- .bike -->
			<?php endif; ?>
		<?php endforeach; ?>
		</div><!-- .bike-period -->
	</div>
</div>


