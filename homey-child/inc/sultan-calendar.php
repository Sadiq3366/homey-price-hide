<?php

/** 
 * Return Unix timestamp from ical date time format 
 * 
 * @param {string} $icalDate A Date in the format YYYYMMDD[T]HHMMSS[Z] or
 *                           YYYYMMDD[T]HHMMSS
 *
 * @return {int} 
 */
function iCalDateToUnixTimestamp($icalDate)
{
    $icalDate = str_replace('T', '', $icalDate);
    $icalDate = str_replace('Z', '', $icalDate);

    $pattern  = '/([0-9]{4})';   // 1: YYYY
    $pattern .= '([0-9]{2})';    // 2: MM
    $pattern .= '([0-9]{2})';    // 3: DD
    $pattern .= '([0-9]{0,2})';  // 4: HH
    $pattern .= '([0-9]{0,2})';  // 5: MM
    $pattern .= '([0-9]{0,2})/'; // 6: SS
    preg_match($pattern, $icalDate, $date);

    // Unix timestamp can't represent dates before 1970
    if ($date[1] <= 1970) {
        return false;
    }
    // Unix timestamps after 03:14:07 UTC 2038-01-19 might cause an overflow
    // if 32 bit integers are used.
    $timestamp = mktime(
        (int)$date[4],
        (int)$date[5],
        (int)$date[6],
        (int)$date[2],
        (int)$date[3],
        (int)$date[1]
    );
    return  $timestamp;
}


add_action('wp_ajax_nopriv_sultan_homey_insert_icalendar_feeds', 'sultan_homey_insert_icalendar_feeds');
add_action('wp_ajax_sultan_homey_insert_icalendar_feeds', 'sultan_homey_insert_icalendar_feeds');
if (!function_exists('sultan_homey_insert_icalendar_feeds')) {
    function sultan_homey_insert_icalendar_feeds()
    {
        $sultan_listing_id = $_POST['sultan_listing_id'];
        $feed_url = $_POST['test_ical'];

        if (empty($feed_url) || !intval($sultan_listing_id) || !is_array($feed_url)) {
            return;
        }

        $temp_array = array();
        $events_data_array = array();

        $ical = $feed_url;
        $events = $ical['EVENTS'];

        if (!empty($events[0])) {
            foreach ($events as $event) {

                $start_time_unix = $end_time_unix = '';
                $feed_name = $_POST['sultan_ical_feed_name'];

                if (isset($event['DTSTART'])) {
                    $start_time_unix = iCalDateToUnixTimestamp($event['DTSTART']);
                }

                if (isset($event['DTEND'])) {
                    $end_time_unix = iCalDateToUnixTimestamp($event['DTEND']);
                }

                if (empty($feed_name)) {
                    $feed_name = $event['SUMMARY'];
                }

                if (!empty($start_time_unix) && !empty($end_time_unix) && !empty($feed_name)) {

                    $temp_array['start_time_unix'] = $start_time_unix;
                    $temp_array['end_time_unix'] = $end_time_unix;
                    $temp_array['feed_name'] = $feed_name;

                    $events_data_array[] = $temp_array;
                }
            }
        }

        $booked_dates_array = get_post_meta($sultan_listing_id, 'reservation_dates', true);

        if (!empty($booked_dates_array)) {
            $events_data_to_unset = array_keys($booked_dates_array, $events_data_array[0]['feed_name']);
            foreach ($events_data_to_unset as $key => $timestamp) {
                unset($booked_dates_array[$timestamp]);
            }
            update_post_meta($sultan_listing_id, 'reservation_dates', $booked_dates_array);
        }

        foreach ($events_data_array as $data) {
            $start_time_unix = $data['start_time_unix'];
            $end_time_unix = $data['end_time_unix'];
            $feed_name = $data['feed_name'];
            sultan_homey_add_listing_booking_dates($sultan_listing_id, $start_time_unix, $end_time_unix, $feed_name);
        }

        echo json_encode(
            array(
                'success' => true,
                'message' => esc_html__("Removed Successfully.", 'homey')
            )
        );
        wp_die();
    }
}

if (!function_exists('sultan_homey_add_listing_booking_dates')) {
    function sultan_homey_add_listing_booking_dates($listing_id, $start_time_unix, $end_time_unix, $feed_name)
    {
        $now = time();
        $daysAgo = $now - 3 * 24 * 60 * 60;

        //change date format and remove hours, mins
        $start_date = gmdate("Y-m-d 0:0:0", $start_time_unix);
        $start_date_unix = strtotime($start_date);
        $end_date = gmdate("Y-m-d 0:0:0", $end_time_unix);
        $end_date_unix = strtotime($end_date);

        if ($end_date_unix < $daysAgo) {
            return;
        }

        $booked_dates_array = get_post_meta($listing_id, 'reservation_dates', true);

        if (!is_array($booked_dates_array) || empty($booked_dates_array)) {
            $booked_dates_array = array();
        }


        $start_date_unix = gmdate("Y-m-d\TH:i:s\Z", $start_date_unix);
        $end_date_unix = gmdate("Y-m-d\TH:i:s\Z", $end_date_unix);

        $check_in = new DateTime($start_date_unix);
        $check_in_unix = $check_in->getTimestamp();
        $check_out = new DateTime($end_date_unix);
        $check_out_unix = $check_out->getTimestamp();

        $booked_dates_array[$check_in_unix] = $feed_name;
        $check_in_unix = $check_in->getTimestamp();

        while ($check_in_unix < $check_out_unix) {

            $booked_dates_array[$check_in_unix] = $feed_name;

            $check_in->modify('tomorrow');
            $check_in_unix = $check_in->getTimestamp();
        }
        //Update booked dates meta
        update_post_meta($listing_id, 'reservation_dates', $booked_dates_array);
    }
}


if (!function_exists('sultan_homey_import_icalendar_feeds')) {
    function sultan_homey_import_icalendar_feeds($listing_id)
    {
        $ical_feeds_meta = get_post_meta($listing_id, 'homey_sultan_ical_feeds_meta', true);

        foreach ($ical_feeds_meta as $key => $value) {
            $feed_name = $value['feed_name'];
            $feed_url = $value['feed_url'];
            //echo $feed_name.' = '.$feed_url.'<br/>';
            sultan_scrap_dates($listing_id, $feed_name, $feed_url);
        }
        /*echo '<pre>';
        print_r($ical_feeds_meta);*/
    }
}

add_action('wp_ajax_nopriv_sultan_scrap_dates', 'sultan_scrap_dates');
add_action('wp_ajax_sultan_scrap_dates', 'sultan_scrap_dates');
if (!function_exists('sultan_scrap_dates')) {
    function sultan_scrap_dates($listing_id = '', $feed_name = '', $feed_url = '')
    {
        $link = [];

        $url = empty($feed_url) ? $_POST['sultan_scrap_link'] : $feed_url;

        $feed_name = empty($feed_name) ? $_POST['sultan_ical_feed_name'] : $feed_name;

        $listing_id = empty($listing_id) ? $_POST['sultan_listing_id'] : $listing_id;

        $temp_array['feed_url'] = esc_url_raw($url);
        $temp_array['feed_name'] = esc_html($feed_name);
        $link[] = $temp_array;

        if (!empty($link[0])) {
            update_post_meta($listing_id, 'homey_sultan_ical_feeds_meta', $link);
        }

        if (isset($link[0]) && !empty($link[0]) && is_array($link)) {

?>
            <script>
                var data = [];
            </script>
            <?php
            $link = trim($link[0]['feed_url']);
            $html = file_get_html($link);
            echo $html->find('section #myTabContent6 #psp-eighth .card script', 0);
            ?>
            <script>
                data.push([slug, "<?= empty($_POST['sultan_villaport_link']) ? '' : $_POST['sultan_villaport_link']; ?>", "<?= $link ?>", giristarihler, cikistarihler]);
            </script>
        <?php
        }
        ?>

        <script>
            if (typeof data !== 'undefined' && typeof data !== null) {
                console.log();
                var header = ['Listing Name', 'Villabooking.ru link', 'Link to scrap the dates', 'check_in', 'check_out'];
                // export_csv_(data, header);
                ical_download(data, "<?php echo admin_url('admin-ajax.php'); ?>", "<?php echo $listing_id ?>", "<?php echo $feed_name ?>");
            }
        </script>

<?php
    }
}

add_action('wp_ajax_sultan_homey_remove_ical_feeds', 'sultan_homey_remove_ical_feeds');
if (!function_exists('sultan_homey_remove_ical_feeds')) {
    function sultan_homey_remove_ical_feeds()
    {
        global $current_user;
        $current_user = wp_get_current_user();
        $userID = $current_user->ID;
        $local = homey_get_localization();
        $allowded_html = array();

        $listing_id = intval($_POST['listing_id']);
        $the_post = get_post($listing_id);
        $post_owner = $the_post->post_author;
        $remove_index = $_POST['remove_index'];

        if (!is_user_logged_in() || $userID === 0) {
            echo json_encode(
                array(
                    'success' => false,
                    'message' => esc_html__('Login required', 'homey')
                )
            );
            wp_die();
        }

        if (!is_numeric($listing_id) || !intval($listing_id)) {
            echo json_encode(
                array(
                    'success' => false,
                    'message' => esc_html__('Something went wrong, please contact site administer', 'homey')
                )
            );
            wp_die();
        }

        if ($userID != $post_owner) {
            echo json_encode(
                array(
                    'success' => false,
                    'message' => esc_html__("You don't have rights to do this.", 'homey')
                )
            );
            wp_die();
        }

        // Remove feed link
        $homey_ical_feeds_meta = get_post_meta($listing_id, 'homey_sultan_ical_feeds_meta', true);
        $feed_for_delete = $homey_ical_feeds_meta[$remove_index]['feed_name'];
        unset($homey_ical_feeds_meta[$remove_index]);
        update_post_meta($listing_id, 'homey_sultan_ical_feeds_meta', $homey_ical_feeds_meta);

        //Remove reserved dates
        $reservation_dates = get_post_meta($listing_id, 'reservation_dates', true);
        $array = array();
        foreach ($reservation_dates as $key => $value) {
            if ($feed_for_delete == $value) {
                unset($reservation_dates[$key]);
            }
        }
        update_post_meta($listing_id, 'reservation_dates', $reservation_dates);

        sultan_homey_import_icalendar_feeds($listing_id);

        echo json_encode(
            array(
                'success' => true,
                'message' => esc_html__("Removed Successfully.", 'homey')
            )
        );
        wp_die();
    }
}

?>