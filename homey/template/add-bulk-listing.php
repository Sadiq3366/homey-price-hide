<?php

/**
 * Template Name: Add Bulk Listing
 */

$download_csv = false;

/*-----------------------------------------------------------------------------------*/
/*  Get Custom periods Bulk
/*-----------------------------------------------------------------------------------*/
if (!function_exists('homey_get_custom_period_bulk')) {
    function homey_get_custom_period_bulk($listing_id, $actions = false)
    {
        if (empty($listing_id)) {
            return;
        }

        $custom_period = [];

        $homey_date_format = homey_option('homey_date_format');

        if ($homey_date_format == 'yy-mm-dd') {
            $h_date_format = 'Y-m-d';
        } elseif ($homey_date_format == 'yy-dd-mm') {
            $h_date_format = 'Y-d-m';
        } elseif ($homey_date_format == 'mm-yy-dd') {
            $h_date_format = 'm-Y-d';
        } elseif ($homey_date_format == 'dd-yy-mm') {
            $h_date_format = 'd-Y-m';
        } elseif ($homey_date_format == 'mm-dd-yy') {
            $h_date_format = 'm-d-Y';
        } elseif ($homey_date_format == 'dd-mm-yy') {
            $h_date_format = 'd-m-Y';
        } elseif ($homey_date_format == 'dd.mm.yy') {
            $h_date_format = 'd.m.Y';
        } else {
            $h_date_format = 'Y-m-d';
        }

        $output = '';
        $i = 0;
        $night_price = '';
        $weekend_price = '';
        $guest_price = '';

        $local = homey_get_localization();

        $hide_fields = homey_option('add_hide_fields');
        $custom_weekend_price = isset($hide_fields['custom_weekend_price']) ? $hide_fields['custom_weekend_price'] : 0;

        $period_array = get_post_meta($listing_id, 'homey_custom_period', true);

        if (empty($period_array)) {
            return;
        }

        if (is_array($period_array)) {
            ksort($period_array);
        }

        foreach ($period_array as $timestamp => $data) {

            $is_consecutive_day = 0;
            $from_date          = new DateTime("@" . $timestamp);
            $to_date            = new DateTime("@" . $timestamp);
            $tomorrrow_date     = new DateTime("@" . $timestamp);

            $tomorrrow_date->modify('tomorrow');
            $tomorrrow_date = $tomorrrow_date->getTimestamp();


            if ($i == 0) {
                $i = 1;


                $night_price   = $data['night_price'];
                $weekend_price = $data['weekend_price'];
                $guest_price   = $data['guest_price'];

                $from_date_unix = $from_date->getTimestamp();

                $custom_period['start_date'][] = $from_date->format($h_date_format);
            }

            if (!array_key_exists($tomorrrow_date, $period_array)) {
                $is_consecutive_day = 1;
            } else {

                if (
                    $period_array[$tomorrrow_date]['night_price']   !=  $night_price ||
                    $period_array[$tomorrrow_date]['weekend_price'] !=  $weekend_price ||
                    $period_array[$tomorrrow_date]['guest_price']   !=  $guest_price
                ) {
                    $is_consecutive_day = 1;
                }
            }

            if ($is_consecutive_day == 1) {

                if ($i == 1) {

                    $to_date_unix = $from_date->getTimestamp();

                    $custom_period['end_date'][] = $from_date->format($h_date_format);

                    $custom_period['nightly_label'][] = explode(' ', homey_formatted_price($night_price, true, true, false))[1];

                    if ($custom_weekend_price != 1) {
                        $custom_period['weekends_label'][] = explode(' ', homey_formatted_price($weekend_price, true, true, false))[1];
                    }

                    $booking_hide_fields = homey_option('booking_hide_fields');
                    if ($booking_hide_fields['guests'] != 1) {
                        $custom_period['addinal_guests_label'][] = explode(' ', homey_formatted_price($guest_price, true, true, false))[1];
                    }
                }
                $i = 0;
                $night_price   = $data['night_price'];
                $weekend_price = $data['weekend_price'];
                $guest_price   = $data['guest_price'];
            }
        } // End foreach
        return $custom_period;
    }
}

function generateCSV()
{
    $csv_data = [];
    $csv = [];
    $homey_prefix = 'homey_';

    $posts = Homey_Query::get_wp_query( array( 'posts_limit' => -1 ) )->posts;
    $post_id = $posts[0]->ID;

    $post = get_post($post_id);

    $post_meta = get_post_meta($post_id);

    
    // $listing_meta_data = get_post_custom( $post_id );
    // echo "<pre>";
    
    foreach ($posts as $key => $value) {
        $post_id = $value->ID;
        
        $room_type      = wp_get_post_terms($post_id, 'room_type');
        $listing_type   = wp_get_post_terms($post_id, 'listing_type');

        // 1st page data
        $csv_data['room_type']              = !empty( $room_type ) ? $room_type[0]->slug : '';
        $csv_data['listing_title']          = $value->post_title;
        $csv_data['description']            = $value->post_content;
        $csv_data['listing_type']           = !empty( $listing_type ) ? $listing_type[0]->slug : '';
        $csv_data['listing_bedrooms']       = get_post_meta($post_id, $homey_prefix . 'listing_bedrooms', true);
        $csv_data['guests']                 = get_post_meta($post_id, $homey_prefix . 'guests', true);
        $csv_data['beds']                   = get_post_meta($post_id, $homey_prefix . 'beds', true);
        $csv_data['baths']                  = get_post_meta($post_id, $homey_prefix . 'baths', true);
        $csv_data['listing_rooms']          = get_post_meta($post_id, $homey_prefix . 'listing_rooms', true);
        $csv_data['listing_size']           = get_post_meta($post_id, $homey_prefix . 'listing_size', true);
        $csv_data['listing_size_unit']      = get_post_meta($post_id, $homey_prefix . 'listing_size_unit', true);
        $csv_data['affiliate_booking_link'] = get_post_meta($post_id, $homey_prefix . 'affiliate_booking_link', true);
        // End 1st page data

        // 2nd page data
        $csv_data['instant_booking']        = get_post_meta($post_id, $homey_prefix . 'instant_booking', true);
        $csv_data['night_price']            = get_post_meta($post_id, $homey_prefix . 'night_price', true);
        $csv_data['weekends_price']         = get_post_meta($post_id, $homey_prefix . 'weekends_price', true);
        $csv_data['weekends_days']          = get_post_meta($post_id, $homey_prefix . 'weekends_days', true);
        $csv_data['priceWeek']              = get_post_meta($post_id, $homey_prefix . 'priceWeek', true);
        $csv_data['priceMonthly']           = get_post_meta($post_id, $homey_prefix . 'priceMonthly', true);
        // array
        $csv_data['extra_price']            = json_encode(get_post_meta($post_id, $homey_prefix . 'extra_prices', true));
        $csv_data['allow_additional_guests'] = get_post_meta($post_id, $homey_prefix . 'allow_additional_guests', true);
        $csv_data['additional_guests_price'] = get_post_meta($post_id, $homey_prefix . 'additional_guests_price', true);
        $csv_data['cleaning_fee']           = get_post_meta($post_id, $homey_prefix . 'cleaning_fee', true);
        $csv_data['cleaning_fee_type']      = get_post_meta($post_id, $homey_prefix . 'cleaning_fee_type', true);
        $csv_data['city_fee']               = get_post_meta($post_id, $homey_prefix . 'city_fee', true);
        $csv_data['city_fee_type']          = get_post_meta($post_id, $homey_prefix . 'city_fee_type', true);
        $csv_data['security_deposit']       = get_post_meta($post_id, $homey_prefix . 'security_deposit', true);
        // End 2nd page data

        // 3rd page options
        $images_ids                     = get_post_meta($post_id, $homey_prefix . 'listing_images', false);
        $csv_data['listing_image_ids']  = !empty( $images_ids ) ? implode('-', $images_ids) : '';
        $csv_data['video_url']          = get_post_meta($post_id, $homey_prefix . 'video_url', true);
        // End 3rd page options

        // 4th page options
        //-----amenities
        $amenities_terms_id = array();
        $amenities = array();
        $amenities_terms = get_the_terms($post_id, 'listing_amenity');
        if ($amenities_terms && !is_wp_error($amenities_terms)) {
            foreach ($amenities_terms as $amenity) {
                $amenities_terms_id[] = intval($amenity->term_id);
            }
        }

        $amenities_ = get_terms('listing_amenity', array('orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false));
        if (!empty($amenities_)) {
            foreach ($amenities_ as $amenity) {
                if (in_array($amenity->term_id, $amenities_terms_id)) {
                    $amenities[] = $amenity->term_id;
                }
            }
        }
        //------- End amenities

        $csv_data['amenities'] = !empty($amenities) ? implode('-', $amenities) : '';

        //------- facilities
        $facilities_terms_id = array();
        $facilities = array();
        $facilities_terms = get_the_terms($post_id, 'listing_facility');
        if ($facilities_terms && !is_wp_error($facilities_terms)) {
            foreach ($facilities_terms as $facility) {
                $facilities_terms_id[] = intval($facility->term_id);
            }
        }

        $facilities_ = get_terms('listing_facility', array('orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false));

        if (!empty($facilities_)) {
            foreach ($facilities_ as $facility) {
                if (in_array($facility->term_id, $facilities_terms_id)) {
                    $facilities[] = $facility->term_id;
                }
            }
        }
        //------- End

        $csv_data['facilities'] = !empty($facilities) ? implode('-', $facilities) : '';
        // End 4th page options

        // 5th page options
        $csv_data['listing_address']            = get_post_meta($post_id, $homey_prefix . 'listing_address', true);
        $csv_data['aptSuit']                    = get_post_meta($post_id, $homey_prefix . 'aptSuit', true);
        $csv_data['locality']                   = homey_get_taxonomy_title($post_id, 'listing_city');
        $csv_data['administrative_area_level_1'] = homey_get_taxonomy_title($post_id, 'listing_state');
        $csv_data['zip']                        = get_post_meta($post_id, $homey_prefix . 'zip', true);
        $csv_data['neighborhood']               = homey_get_taxonomy_title($post_id, 'listing_area');
        $csv_data['country']                    = homey_get_taxonomy_title($post_id, 'listing_country');
        $csv_data['lat']                        = get_post_meta($post_id, $homey_prefix . 'geolocation_lat', true);
        $csv_data['lng']                        = get_post_meta($post_id, $homey_prefix . 'geolocation_long', true);
        // End 5th page options

        // 6th page options
        $csv_data['accomodation'] = json_encode(get_post_meta($post_id, $homey_prefix . 'accomodation', true));
        // End 6th page options

        // 7th page options
        $csv_data['cancellation_policy']    = get_post_meta($post_id, $homey_prefix . 'cancellation_policy', true);
        $csv_data['min_book_days']          = get_post_meta($post_id, $homey_prefix . 'min_book_days', true);
        $csv_data['min_book_weeks']         = get_post_meta($post_id, $homey_prefix . 'min_book_weeks', true);
        $csv_data['min_book_months']        = get_post_meta($post_id, $homey_prefix . 'min_book_months', true);
        $csv_data['max_book_days']          = get_post_meta($post_id, $homey_prefix . 'max_book_days', true);
        $csv_data['max_book_weeks']         = get_post_meta($post_id, $homey_prefix . 'max_book_weeks', true);
        $csv_data['max_book_months']        = get_post_meta($post_id, $homey_prefix . 'max_book_months', true);
        $csv_data['checkin_after']          = get_post_meta($post_id, $homey_prefix . 'checkin_after', true);
        $csv_data['checkout_before']        = get_post_meta($post_id, $homey_prefix . 'checkout_before', true);
        $csv_data['smoke']                  = get_post_meta($post_id, $homey_prefix . 'smoke', true);
        $csv_data['pets']                   = get_post_meta($post_id, $homey_prefix . 'pets', true);
        $csv_data['party']                  = get_post_meta($post_id, $homey_prefix . 'party', true);
        $csv_data['children']               = get_post_meta($post_id, $homey_prefix . 'children', true);
        $csv_data['additional_rules']       = get_post_meta($post_id, $homey_prefix . 'additional_rules', true);
        // End 7th page options

        //-----ical
        //$csv_data['ical_feeds_meta'] = json_encode(get_post_meta($post_id, $homey_prefix . 'ical_feeds_meta', true));
        //$csv_data['ical_feeds_meta'] = get_post_meta($post_id, $homey_prefix . 'ical_feeds_meta', true);

        $csv_data['ical_feed_name'] = [];
        $csv_data['ical_feed_url'] = [];

        $ical_feeds_meta = get_post_meta($post_id, $homey_prefix . 'ical_feeds_meta', true);
        if (!empty($ical_feeds_meta)) {
            foreach ($ical_feeds_meta as $key => $value) {
                $csv_data['ical_feed_name'][] = $value['feed_name'];
                $csv_data['ical_feed_url'][] = $value['feed_url'];
            }
        }

        $csv_data['ical_feed_name'] = !empty($csv_data['ical_feed_name']) ? implode(',', $csv_data['ical_feed_name']) : '';
        $csv_data['ical_feed_url']  = !empty($csv_data['ical_feed_url'])  ? implode(',', $csv_data['ical_feed_url'])  : '';
        // $csv_data['ical_id']                        = get_post_meta($post_id, $homey_prefix . 'ical_id', true);
        // $csv_data['icalendar_file_url_with_ics']    = get_post_meta($post_id, 'icalendar_file_url_with_ics', true);
        // End

        //-----custom price
        //$csv_data['custom_period']          = get_post_meta($post_id, $homey_prefix . 'custom_period', true);
        $custom_period_bulk                 = homey_get_custom_period_bulk($post_id);

        $csv_data['custom_price_date_in']   = !empty($custom_period_bulk['start_date'])           ? implode(',', $custom_period_bulk['start_date'])           : '';
        $csv_data['custom_price_date_out']  = !empty($custom_period_bulk['end_date'])             ? implode(',', $custom_period_bulk['end_date'])             : '';
        $csv_data['custom_price_per_day']   = !empty($custom_period_bulk['nightly_label'])        ? implode(',', $custom_period_bulk['nightly_label'])        : '';
        $csv_data['custom_price_additional'] = !empty($custom_period_bulk['addinal_guests_label']) ? implode(',', $custom_period_bulk['addinal_guests_label']) : '';
        $csv_data['custom_price_weekend']   = !empty($custom_period_bulk['weekends_label'])       ? implode(',', $custom_period_bulk['weekends_label'])       : '';
        // End

        //-----reserve_period
        $csv_data['check_in_date']  = get_post_meta($post_id, 'listing_renter', true);
        $csv_data['check_out_date'] = '';
        $csv_data['period_note']    = '';
        //

        // $csv_data['services']           = get_post_meta($post_id, $homey_prefix . 'services', true);
        // $csv_data['featured']           = get_post_meta($post_id, $homey_prefix . 'featured', true);

        // $csv_data['total_guests_plus_additional_guests']    = get_post_meta($post_id, $homey_prefix . 'total_guests_plus_additional_guests', true);
        // $csv_data['mon_fri_closed']                         = get_post_meta($post_id, $homey_prefix . 'mon_fri_closed', true);
        // $csv_data['sat_closed ']                            = get_post_meta($post_id, $homey_prefix . 'sat_closed', true);

        // //
        // $csv_data['reservation_dates']              = !empty( get_post_meta($post_id, 'reservation_dates', true) ) ? implode(',', get_post_meta($post_id, 'reservation_dates', true)) : '';

        // $csv_data['firsttime_is_admin_approved']    = get_post_meta($post_id, $homey_prefix . 'firsttime_is_admin_approved', true);
        // $csv_data['price_postfix']                  = get_post_meta($post_id, $homey_prefix . 'price_postfix', true);
        // $csv_data['num_additional_guests']          = get_post_meta($post_id, $homey_prefix . 'num_additional_guests', true);

        // //
        
        // $csv_data['listing_location']   = get_post_meta($post_id, $homey_prefix . 'listing_location', true);
        // $csv_data['listing_map']        = get_post_meta($post_id, $homey_prefix . 'listing_map', true);
        // $csv_data['show_map']           = get_post_meta($post_id, $homey_prefix . 'show_map', true);
        
        
        $csv_data['booking_type'] = homey_booking_type_by_id($post_id);
        
        // TEMPLATEPATH
        $csv_data['featured_image_id'] = get_post_meta($post_id, '_thumbnail_id', true);
        $csv_data['listing_id'] = $post_id;


        $csv[] = $csv_data;
        // print_r( $csv );

    }

    $file_name = "edit_listings.csv";
    $path = $_SERVER['DOCUMENT_ROOT'] . "/wp-content/uploads/download/";

    // if( !file_exists( $path ))
    // {
    //     mkdir( $path );
    // }

    $file = fopen( $path . $file_name, "w");

    if (!empty($csv)) {
        foreach ($csv as $key => $line) {
            if ($key === 0) {
                $temp = [];
                foreach ($line as $key => $value) {
                    $temp[] = $key;
                }
                fputcsv($file, $temp);
            }
            fputcsv($file, $line);
        }
    }

    fclose($file);

    // print_r( $listing_type );
}

// generateCSV();
// echo "<pre>";
// print_r( $post_meta );
// exit;


if (!is_user_logged_in()) {
    wp_redirect(home_url('/'));
}

if (isset($_POST['submit'])) {
    mycallADD();
}

if (isset($_POST['export'])) {
    generateCSV();
    $download_csv = true;
}

function mycallADD()
{
    $handle = fopen($_FILES['filename']['tmp_name'], "r");
    $headers = fgetcsv($handle, 1000, ",");
    $arr = array(
        'orderby' => 'name',
        'order' => 'ASC',
        'hide_empty' => false,
        'parent' => 0
    );
    $listing_type = get_terms(
        array(
            "listing_type"
        ),
        $arr
    );
    $Room_type = get_terms(
        array(
            "room_type"
        ),
        $arr
    );

    $new_listing = array(
        'post_type' => 'listing'
    );
    $Amenities = get_terms(
        array(
            "listing_amenity"
        ),
        $arr
    );
    $Facilities = get_terms(
        array(
            "listing_facility"
        ),
        $arr
    );


    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

        // echo"<pre>";
        // print_r($data);
        // exit;
        // 1st page data
        $_POST['room_type']        = get_id_of_custom_fields($Room_type, $data[0]);
        $_POST['listing_title']    = $data[1];
        $_POST['description']      = $data[2];
        $_POST['listing_type']     = get_id_of_custom_fields($listing_type, $data[3]);//htmlspecialchars_decode($data[3]);
        $_POST['listing_bedrooms'] = $data[4];
        $_POST['guests']           = $data[5];
        $_POST['beds']             = $data[6];
        $_POST['baths']            = $data[7];
        $_POST['listing_rooms']    = $data[8];
        $_POST['listing_size']     = $data[9];
        $_POST['listing_size_unit'] = $data[10];
        $_POST['affiliate_booking_link']     = $data[11];
        // 1st page data

        // 2nd page data
        $_POST['instant_booking']  = $data[12];
        $_POST['night_price']      = $data[13];
        $_POST['weekends_price']   = $data[14];
        $_POST['weekends_days']    = $data[15];
        $_POST['priceWeek']        = $data[16];
        $_POST['priceMonthly']     = $data[17];
        $_POST['extra_price']      = json_decode($data[18], true);
        $_POST['allow_additional_guests']        = $data[19];
        $_POST['additional_guests_price']        = $data[20];
        $_POST['cleaning_fee']     = $data[21];
        $_POST['cleaning_fee_type'] = $data[22];
        $_POST['city_fee']        = $data[23];
        $_POST['city_fee_type']   = $data[24];
        $_POST['security_deposit']     = $data[25];
        // 2nd page data

        // 3rd page options
        $_POST['listing_image_ids'] = !empty( $data[26] ) ? explode('-', $data[26]) : '';
        $_POST['video_url']         = $data[27];
        // 3rd page options

        // 4th page options
        $anem = (explode("-", $data[28]));
        // for ($i = 0; $i < count($anem); $i++) {
        //     $anem[$i] = get_id_of_custom_fields($Amenities, $anem[$i]);
        // }
        $_POST['listing_amenity']        = $anem;

        $facl = (explode("-", $data[29]));
        // for ($i = 0; $i < count($facl); $i++) {
        //     $facl[$i] = get_id_of_custom_fields($Facilities, $facl[$i]);
        // }
        $_POST['listing_facility']       = $facl;
        // 4th page options

        // 5th page options
        $_POST['listing_address']               = $data[30];
        $_POST['aptSuit']                       = $data[31];
        $_POST['locality']                      = $data[32];
        $_POST['administrative_area_level_1']   = $data[33];
        $_POST['zip']                           = $data[34];
        $_POST['neighborhood']                  = $data[35];
        $_POST['country']                       = $data[36];
        $_POST['lat']                           = $data[37];
        $_POST['lng']                           = $data[38];
        // 5th page options 


        // 6th page options
        $_POST['homey_accomodation']  = json_decode($data[39], true);
        // 6th page options

        // 7th page options
        $_POST['cancellation_policy']   = $data[40];
        $_POST['min_book_days']         = $data[41];
        $_POST['min_book_weeks']        = $data[42];
        $_POST['min_book_months']       = $data[43];
        $_POST['max_book_days']         = $data[44];
        $_POST['max_book_weeks']        = $data[45];
        $_POST['max_book_months']       = $data[46];
        $_POST['checkin_after']         = $data[47];
        $_POST['checkin_before']        = $data[48];
        $_POST['smoke']                 = $data[49];
        $_POST['pets']                  = $data[50];
        $_POST['party']                 = $data[51];
        $_POST['children']              = $data[52];
        $_POST['additional_rules']      = $data[53];
        // 7th page options

        //-----ical
        $_POST['ical_feed_name']  = !empty($data[54]) ? $data[54] : '';
        $_POST['ical_feed_url']   = !empty($data[55]) ? $data[55] : '';
        //

        //-----custom price
        $_POST['custom_price_date_in']      = !empty($data[56]) ? explode(',', $data[56]) : '';
        $_POST['custom_price_date_out']     = explode(',', $data[57]);
        $_POST['custom_price_per_day']      = explode(',', $data[58]);
        $_POST['custom_price_additional']   = explode(',', $data[59]);
        $_POST['custom_price_weekend']      = explode(',', $data[60]);
        //

        //-----reserve_period
        $_POST['check_in_date']     = !empty($data[61]) ? explode(',', $data[61]) : '';
        $_POST['check_out_date']    = !empty($data[62]) ? explode(',', $data[62]) : '';
        $_POST['period_note']       = !empty($data[63]) ? explode(',', $data[63]) : '';
        //
        
        //-----featured_image_id
        $_POST['featured_image_id'] = !empty( $data[65] ) ? $data[65] : '';
        //
        
        //-----listing id
        $_listing_id = !empty( $data[66] ) ? $data[66] : '';
        //
        
        
        //-----Check
        $_POST['is_listing_bulk_upload'] = 1;
        //
        
        $_POST['draft_listing_id'] = !empty( $_listing_id ) ? $_listing_id : '';
        
        
        if ( empty( $_POST['draft_listing_id']) ) {
            $_POST['booking_type'] = homey_booking_type();
            $_POST['listing_featured'] = 0;
            $_POST['action']         =  "homey_add_listing";
        } else {
            $_POST['booking_type'] = !empty( $data[64] ) ? $data[64] : '';
            $_POST['action']         =  "update_listing";
        }

        $listing_id = listing_submission_filter($new_listing);

        //-----reserve_period
        if (
            isset($_POST['check_in_date'])  && !empty($_POST['check_in_date'])
            || isset($_POST['check_out_date']) && !empty($_POST['check_out_date'])
        ) {
            $n = 0;
            $_POST['listing_id'] = $listing_id;
            foreach ($_POST['check_in_date'] as $key => $value) {
                $_POST['check_in_date']     = $value;
                $_POST['check_out_date']    = explode(',', $data[62])[$n];
                $_POST['period_note']       = !empty($data[63]) ? explode(',', $data[63])[$n] : '';

                homey_reserve_period_host();

                $n++;
            }
        }
        //

        //----- Custom-Bulk ---- Save listing custom periods
        if (
            isset($_POST['custom_price_date_in'][0]) && !empty($_POST['custom_price_date_in'][0]) ||
            isset($_POST['custom_price_date_in'])    && !empty($_POST['custom_price_date_in'])
        ) {
            $custom_price_date_in       = $_POST['custom_price_date_in'];
            $custom_price_date_out      = $_POST['custom_price_date_out'];
            $custom_price_per_day       = $_POST['custom_price_per_day'];
            $custom_price_additional    = $_POST['custom_price_additional'];
            $custom_price_weekend       = $_POST['custom_price_weekend'];

            $i = 0;
            foreach ($custom_price_date_in as $start_date) {
                $_POST['ical_is_bulk'] = 1;

                $_POST['start_date']            = $start_date;
                $_POST['end_date']              = $custom_price_date_out[$i];
                $_POST['night_price']           = $custom_price_per_day[$i];
                $_POST['additional_guest_price'] = $custom_price_additional[$i];
                $_POST['weekend_price']         = $custom_price_weekend[$i];

                homey_add_custom_period();

                $i++;
            }
            $_POST['ical_is_bulk']          = '';
            $_POST['custom_price_date_in']  = '';
        }
        //-----
    }

    //exit;

    fclose($handle);
    wp_redirect(home_url('/my-listings'));
    die();
}

function get_id_of_custom_fields($parent_taxonomy, $custom_field)
{
    foreach ($parent_taxonomy as $term) {
        $n = $term->slug;
        // $n = $term->name;
        // $n = str_replace('&amp;', '', $n);
        // $custom_field = str_replace('&', '', $custom_field);
        if (!strcmp(strval($n), strval($custom_field))) {
            return $term->term_id;
        }
    }
}

get_header();
?>

<section id="body-area">

    <div class="dashboard-page-title" style="display: grid">
        <h1 style="grid-column: 1">
            <?php

            the_title();
            ?>
        </h1>
        <div style="grid-column: 2; display: flex">
            <form action='' method='post'>
                <button type="submit" name="export" class="btn btn-success btn-step-submit"> <?php echo esc_html__('Export CSV', 'homey') ?> </button>
            </form>
            <?php if( $download_csv ) {
                echo '<p><a download="file" class="btn btn-info" style="margin-left: 50px" href="'. wp_upload_dir()['baseurl'] .'/download/edit_listings.csv">Download Here</a></p>';
            } ?>
        </div>
    </div><!-- .dashboard-page-title -->

    <?php get_template_part('template-parts/dashboard/side-menu'); ?>

    <div class="user-dashboard-right dashboard-with-sidebar">
        <div class="dashboard-content-area">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="block">
                            <form enctype='multipart/form-data' action='' method='post'>

                                <label>Select CSV File</label>

                                <input size='50' type='file' name='filename'>
                                </br>
                                <button type="submit" name="submit" class="btn btn-success btn-step-submit btn-xs-full-width action"><?php echo esc_attr($homey_local['submit_btn']); ?></button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section><!-- #body-area -->


<?php get_footer(); ?>