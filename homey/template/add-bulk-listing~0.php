<?php

/**
 * Template Name: Add Bulk Listing
 */

if (!is_user_logged_in()) {
    wp_redirect(home_url('/'));
}

if (isset($_POST['submit'])) {
    mycallADD();
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
        //exit;
        // 1st page data
        $_POST['room_type']        = get_id_of_custom_fields($Room_type, $data[0]);
        $_POST['listing_title']    = $data[1];
        $_POST['description']      = $data[2];
        $_POST['listing_type']     =  get_id_of_custom_fields($listing_type, $data[3]);
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
        $_POST['extra_price']      = $data[18];
        $_POST['allow_additional_guests']        = $data[19];
        $_POST['additional_guests_price']        = $data[20];
        $_POST['cleaning_fee']     = $data[21];
        $_POST['cleaning_fee_type'] = $data[22];
        $_POST['city_fee']        = $data[23];
        $_POST['city_fee_type']   = $data[24];
        $_POST['security_deposit']     = $data[25];
        // 2nd page data

        // 3rd page options
        $_POST['listing_image_ids']      = $data[26];
        $_POST['video_url']        = $data[27];
        // 3rd page options

        // 4th page options
        $anem = (explode(",", $data[28]));

        for ($i = 0; $i < count($anem); $i++) {
            $anem[$i] = get_id_of_custom_fields($Amenities, $anem[$i]);
        }

        $_POST['listing_amenity']        = $anem;
        $facl = (explode(",", $data[29]));
        for ($i = 0; $i < count($facl); $i++) {
            $facl[$i] = get_id_of_custom_fields($Facilities, $facl[$i]);
        }
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
        $_POST['homey_accomodation']  = json_decode( $data[39], true );

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
        $_POST['custom_price_date_in']      = !empty( $data[56] ) ? explode(',', $data[56]) : '';
        $_POST['custom_price_date_out']     = explode(',', $data[57]);
        $_POST['custom_price_per_day']      = explode(',', $data[58]);
        $_POST['custom_price_additional']   = explode(',', $data[59]);
        $_POST['custom_price_weekend']      = explode(',', $data[60]);
        //

        //-----reserve_period
        $_POST['check_in_date']     = !empty( $data[61] ) ? explode(',', $data[61]) : '';
        $_POST['check_out_date']    = !empty( $data[62] ) ? explode(',', $data[62]) : '';
        $_POST['period_note']       = !empty( $data[63] ) ? explode(',', $data[63]) : '';
        //
        
        //-----Check
        $_POST['is_listing_bulk_upload'] = 1;
        //
        
        $_POST['action']         =  "homey_add_listing";
        
        $listing_id = listing_submission_filter($new_listing);
        
        //-----reserve_period
        if( isset( $_POST['check_in_date'] )  && !empty( $_POST['check_in_date'] ) 
        || isset( $_POST['check_out_date'] ) && !empty( $_POST['check_out_date'] ) )
        {
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
        if( isset( $_POST['custom_price_date_in'][0] ) && !empty( $_POST['custom_price_date_in'][0] ) ||
            isset( $_POST['custom_price_date_in'] )    && !empty( $_POST['custom_price_date_in'] ))
        {
            $custom_price_date_in       = $_POST['custom_price_date_in'];
            $custom_price_date_out      = $_POST['custom_price_date_out'];
            $custom_price_per_day       = $_POST['custom_price_per_day'];
            $custom_price_additional    = $_POST['custom_price_additional'];
            $custom_price_weekend       = $_POST['custom_price_weekend'];

            $i = 0;
            foreach( $custom_price_date_in as $start_date )
            {
                $_POST['ical_is_bulk'] = 1;
                
                $_POST['start_date']            = $start_date;
                $_POST['end_date']              = $custom_price_date_out[$i];
                $_POST['night_price']           = $custom_price_per_day[$i];
                $_POST['additional_guest_price']= $custom_price_additional[$i];
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
        $n = $term->name;
        $n = str_replace('&amp;', '', $n);
        $custom_field = str_replace('&', '', $custom_field);
        if (!strcmp(strval($n), strval($custom_field))) {
            return $term->term_id;
        }
    }
}

get_header();
?>

<section id="body-area">

    <div class="dashboard-page-title">
        <h1>
            <?php

            the_title();
            ?>
        </h1>
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