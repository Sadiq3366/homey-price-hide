<?php
/**
 * Template Name: Instant Experience Booking
 */

$no_login_needed_for_booking = homey_option('no_login_needed_for_booking');

if ( $no_login_needed_for_booking == 'no' && !is_user_logged_in() ) {
    wp_redirect(  home_url('/') );
    return false;
}
get_header();

global $post, $current_user, $homey_prefix, $homey_local;

$experience_id = isset($_GET['experience_id']) ? $_GET['experience_id'] : '';

get_template_part('template-parts/instance-booking/experiences/slots');

get_footer(); ?>
