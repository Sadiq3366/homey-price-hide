<?php 
global $post, $current_user, $homey_prefix, $homey_local, $listing_id;
wp_get_current_user();

$listing_id = $post->ID;
$price_per_night = get_post_meta($listing_id, $homey_prefix.'night_price', true);
$instant_booking = get_post_meta($listing_id, $homey_prefix.'instant_booking', true);
$offsite_payment = homey_option('off-site-payment');
$Price_no =get_post_meta($listing_id, $homey_prefix.'yes_no', true);

$prefilled = homey_get_dates_for_booking();

$key = '';
$userID      =   $current_user->ID;
$fav_option = 'homey_favorites-'.$userID;
$fav_option = get_option( $fav_option );
if( !empty($fav_option) ) {
    $key = array_search($post->ID, $fav_option);
}

$price_separator = homey_option('currency_separator');

if( $key != false || $key != '' ) {
    $favorite = $homey_local['remove_favorite'];
    $heart = 'fa-heart';
} else {
    $favorite = $homey_local['add_favorite'];
    $heart = 'fa-heart-o';
}
$listing_price = homey_get_price();
?>
<div id="homey_remove_on_mobile" class="sidebar-booking-module hidden-sm hidden-xs">
	<div class="block">
		<div class="sidebar-booking-module-header">
			<div class="block-body-sidebar">
				
					<?php 
					if(!empty($Price_no)){
                        echo 'On Request';
					}
				else if(!empty($listing_price)) { ?>

					<span class="item-price">
					<?php 	
					$is_txt_from = true;
					$sa_nights_in_diff = 1;

					if(isset($_GET['arrive']) && $_GET['depart']){
						$sa_guests = isset($_GET['guest']) ? $_GET['guest'] : 1;
						$listing_price = homey_calculate_booking_cost_ajax_nightly($listing_id, $_GET['arrive'], $_GET['depart'], $sa_guests, null, 1);
						$is_txt_from = false;
						$diff = strtotime($_GET['arrive']) - strtotime($_GET['depart']);
        				$sa_nights_in_diff = ceil(abs($diff / 86400)) < 1 ? 1 : ceil(abs($diff / 86400));
					}
					echo homey_formatted_price($listing_price, false, true, $is_txt_from); ?><sub><?php echo esc_attr($price_separator); ?><?php echo homey_get_price_label($sa_nights_in_diff, $sa_nights_in_diff);?></sub>
					</span>

					<?php } else { 
						echo '<span class="item-price free">'.esc_html__('Free', 'homey').'</span>';
					}?>
				
			</div><!-- block-body-sidebar -->
		</div><!-- sidebar-booking-module-header -->
		<div class="sidebar-booking-module-body">
			<div class="homey_notification block-body-sidebar">

				<?php 
				if( homey_affiliate_booking_link() ) { ?>

					<a href="<?php echo homey_affiliate_booking_link(); ?>" target="_blank" class="btn btn-full-width btn-primary"><?php echo esc_html__('Book Now', 'homey'); ?></a>

				<?php 
				} else { ?>
					<div id="single-listing-date-range" class="search-date-range">
						<div class="search-date-range-arrive">
							<input name="arrive" value="<?php echo esc_attr($prefilled['arrive']); ?>" readonly type="text" class="form-control check_in_date" autocomplete="off" placeholder="<?php echo esc_attr(homey_option('srh_arrive_label')); ?>">
						</div>
						<div class="search-date-range-depart">
							<input name="depart" value="<?php echo esc_attr($prefilled['depart']); ?>" readonly type="text" class="form-control check_out_date" autocomplete="off" placeholder="<?php echo esc_attr(homey_option('srh_depart_label')); ?>">
						</div>
						
						<div id="single-booking-search-calendar" class="search-calendar single-listing-booking-calendar-js clearfix" style="display: none;">
							<?php homeyAvailabilityCalendar(); ?>

							<div class="calendar-navigation custom-actions">
		                        <button class="listing-cal-prev btn btn-action pull-left disabled"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>
		                        <button class="listing-cal-next btn btn-action pull-right"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>
		                    </div><!-- calendar-navigation -->	                
						</div>
					</div>
					
					<?php get_template_part('single-listing/booking/guests'); ?>

					<?php get_template_part('single-listing/booking/extra-prices'); ?>

					<?php if( $offsite_payment == 0 ) { ?>
					<div class="search-message">
						<textarea name="guest_message" class="form-control" rows="3" placeholder="<?php echo esc_html__('Introduce yourself to the host', 'homey'); ?>"></textarea>
					</div>
					<?php } ?>
					
					<div class="homey_preloader">
						<?php get_template_part('template-parts/spinner'); ?>
					</div>				
					<div id="homey_booking_cost" class="payment-list"></div>	

					<input type="hidden" name="listing_id" id="listing_id" value="<?php echo intval($listing_id); ?>">
					<input type="hidden" name="reservation-security" id="reservation-security" value="<?php echo wp_create_nonce('reservation-security-nonce'); ?>"/>
					
					<?php if($instant_booking && $offsite_payment == 0 ) { ?>
					<!-- 	<button id="instance_reservation" type="button" class="btn btn-full-width btn-primary"><?php echo esc_html__('Instant Booking', 'homey'); ?></button> -->
					<?php } else { ?> 
					<!-- 	<button id="request_for_reservation" type="button" class="btn btn-full-width btn-primary"><?php echo esc_html__('Request to Book', 'homey'); ?></button>
						 -->
					<?php } ?>
					
				<?php } ?>
				
				<button data-whats-app-message-body="<?php echo homey_option('homey_sa_whatsapp_message'); ?>" data-listing-title="<?php echo the_title();?>" data-listing-link="<?php echo get_permalink();?>" data-wa-number="<?php echo homey_option('homey_sa_whatsapp_number'); ?>" id="request_for_reservation_wa" type="button" class="btn btn-full-width btn-primary"><?php echo esc_html__(homey_option('homey_sa_whatsapp_btn'), 'homey'); ?></button>
				<div class="text-center text-small"><i class="fa fa-info-circle"></i> <?php echo esc_html__("You won't be charged yet", 'homey'); ?></div>
			</div><!-- block-body-sidebar -->
		</div><!-- sidebar-booking-module-body -->
		
	</div><!-- block -->
</div><!-- sidebar-booking-module -->
<div class="sidebar-booking-module-footer">
	<div class="block-body-sidebar">

		<?php if(homey_option('detail_favorite') != 0) { ?>
		<button type="button" data-listid="<?php echo intval($post->ID); ?>" class="add_fav btn btn-full-width btn-grey-outlined"><i class="fa <?php echo esc_attr($heart); ?>" aria-hidden="true"></i> <?php echo esc_attr($favorite); ?></button>
		<?php } ?>
		
		<?php if(homey_option('detail_contact_form') != 0 && homey_option('hide-host-contact') !=1 ) { ?>
		<button type="button" data-toggle="modal" data-target="#modal-contact-host" class="btn btn-full-width btn-grey-outlined"><?php echo esc_attr($homey_local['pr_cont_host']); ?></button>
		<?php } ?>
		
		<?php if(homey_option('print_button') != 0) { ?>
		<button type="button" id="homey-print" class="btn btn-full-width btn-blank" data-listing-id="<?php echo intval($listing_id);?>">
			<i class="fa fa-print" aria-hidden="true"></i> <?php echo esc_attr($homey_local['print_label']); ?>
		</button>
		<?php } ?>
	</div><!-- block-body-sidebar -->
	
	<?php 
	if(homey_option('detail_share') != 0) {
		get_template_part('single-listing/share'); 
	}
	?>
</div><!-- sidebar-booking-module-footer -->
