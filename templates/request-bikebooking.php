<?php sk_header(); ?>
	<div class="container-fluid">

		<div class="single-post__row">

			<aside class="sk-sidebar single-post__sidebar">

				<a href="#post-content" class="focus-only"><?php _e('Hoppa över sidomeny', 'sundsvall_se'); ?></a>

				<?php do_action('sk_page_helpmenu'); ?>

			</aside>

			<div class="single-post__content" id="post-content">

				<?php Sk_Bike_Booking_Public::requests(); ?>
				<div class="clearfix"></div>

				<?php do_action('sk_after_page_content'); ?>

			</div>

		</div> <?php //.row ?>

	</div> <?php //.container-fluid ?>

<?php get_footer(); ?>
