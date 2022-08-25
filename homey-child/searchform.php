<?php 
global $post, $homey_local, $homey_prefix;
$advanced_filter = (int) homey_option('advanced_filter');
$search_width = homey_option('search_width');

$location_search = isset($_GET['location_search']) ? $_GET['location_search'] : '';
$country = isset($_GET['search_country']) ? $_GET['search_country'] : '';
$city = isset($_GET['search_city']) ? $_GET['search_city'] : '';
$area = isset($_GET['search_area']) ? $_GET['search_area'] : '';

$arrive = isset($_GET['arrive']) ? $_GET['arrive'] : '';
$depart = isset($_GET['depart']) ? $_GET['depart'] : '';
$guest = isset($_GET['guest']) ? $_GET['guest'] : '';

$lat = isset($_GET['lat']) ? $_GET['lat'] : '';
$lng = isset($_GET['lng']) ? $_GET['lng'] : '';

$class = '';
if($advanced_filter != 1) {
	$class = 'without-filters';
}

$listing_type_pre = '';
if(isset($_GET['listing_type'])) {
    $listing_type_pre = $_GET['listing_type'];
}

$listing_country_pre = '';
if(isset($_GET['country'])) {
    $listing_country_pre = $_GET['country'];
}

$listing_state_pre = '';
if(isset($_GET['state'])) {
    $listing_state_pre = $_GET['state'];
}

$listing_city_pre = '';
if(isset($_GET['city'])) {
    $listing_city_pre = $_GET['city'];
}

$listing_area_pre = '';
if(isset($_GET['area'])) {
	$listing_area_pre = $_GET['area'];
}

$get_start_time = '';
if(isset($_GET['start'])) {
    $get_start_time = $_GET['start'];
}

$get_end_time = '';
if(isset($_GET['end'])) {
    $get_end_time = $_GET['end'];
}

$location_field = homey_option('location_field');
if($location_field == 'geo_location') {
    $location_classes = "search-destination search-destination-js";
} elseif($location_field == 'keyword') {
    $location_classes = "search-destination search-destination-js";
} else {
    $location_classes = "search-destination with-select search-destination-js";
}

$radius_class = '';
if( homey_option('enable_radius') ) {
    $radius_class = 'search-destination-geolocation search-destination-js';
}

?>
<style type="text/css">
	#searchform button{ background-color: transparent !important; }
	#searchform li.active{  background-color: #e85d74 !important; }
	#searchform li:hover{   background-color: #3b4249 !important; }
	#searchform a:before{ content: ""; }
</style>
<form role="search" method="get" id="searchform" class="searchform" action="<?php echo homey_get_search_result_page(); ?>">
		<div style="width: 88%;" class="<?php echo esc_attr($location_classes).' '.esc_attr($radius_class); ?>">
							
							
                            <?php if($location_field == 'geo_location') { ?>
                            <label class="animated-label"><?php echo esc_attr(homey_option('srh_whr_to_go')); ?></label>    
                            <input type="text" name="location_search" autocomplete="off" id="location_search_banner" value="<?php echo esc_attr($location_search); ?>" class="form-control input-search" placeholder="<?php echo esc_attr(homey_option('srh_whr_to_go')); ?>">
							<input type="hidden" name="search_city" data-value="<?php echo esc_attr($city); ?>" value="<?php echo esc_attr($city); ?>"> 
							<input type="hidden" name="search_area" data-value="<?php echo esc_attr($area); ?>" value="<?php echo esc_attr($area); ?>"> 
							<input type="hidden" name="search_country" data-value="<?php echo esc_attr($country); ?>" value="<?php echo esc_attr($country); ?>">
                            
                            <input type="hidden" name="lat" value="<?php echo esc_attr($lat); ?>">
                            <input type="hidden" name="lng" value="<?php echo esc_attr($lng); ?>">

							<button type="reset" class="btn clear-input-btn"><i class="fa fa-times" aria-hidden="true"></i></button>

                            <?php } elseif($location_field == 'keyword') { ?>

                                        <label class="animated-label"><?php echo esc_attr(homey_option('srh_whr_to_go')); ?></label>
                                        <input type="text" name="keyword" autocomplete="off" value="<?php echo isset($_GET['keyword']) ? $_GET['keyword'] : ''; ?>" class="form-control input-search" placeholder="<?php echo esc_attr(homey_option('srh_whr_to_go')); ?>">

                                    <?php } elseif($location_field == 'country') { ?>

                                <input value="<?php echo esc_attr($listing_country_pre); ?>" placeholder="<?php echo esc_attr(homey_option('srh_whr_to_go')); ?>" name="country" class="selectpicker" data-live-search="true" list="search_countries_datalist">
                               <datalist id="search_countries_datalist">
                                <?php
                                $listing_country = get_terms (
                                    array(
                                        "listing_country"
                                    ),
                                    array(
                                        'orderby' => 'name',
                                        'order' => 'ASC',
                                        'hide_empty' => false,
                                        'parent' => 0
                                    )
                                );
                                homey_data_list_options('listing_country', $listing_country, $listing_country_pre );
                                ?>
                                </datalist>
                            <?php } elseif($location_field == 'state') { ?>

                            <select name="state" class="selectpicker" data-live-search="true">
                            <?php
                            // All Option
                            echo '<option value="">'.esc_attr(homey_option('srh_whr_to_go')).'</option>';

                            $listing_state = get_terms (
                                array(
                                    "listing_state"
                                ),
                                array(
                                    'orderby' => 'name',
                                    'order' => 'ASC',
                                    'hide_empty' => false,
                                    'parent' => 0
                                )
                            );
                            homey_hirarchical_options('listing_state', $listing_state, $listing_state_pre );
                            ?>
                            </select>
                            
                            <?php } elseif($location_field == 'city') { ?>

                            <select name="city" class="selectpicker" data-live-search="true">
                            <?php
                            // All Option
                            echo '<option value="">'.esc_attr(homey_option('srh_whr_to_go')).'</option>';

                            $listing_city = get_terms (
                                array(
                                    "listing_city"
                                ),
                                array(
                                    'orderby' => 'name',
                                    'order' => 'ASC',
                                    'hide_empty' => false,
                                    'parent' => 0
                                )
                            );
                            homey_hirarchical_options('listing_city', $listing_city, $listing_city_pre );
                            ?>
                            </select>

                            <?php } elseif($location_field == 'area') { ?>

                            <select name="area" class="selectpicker" data-live-search="true">
                            <?php
                            // All Option
                            echo '<option value="">'.esc_attr(homey_option('srh_whr_to_go')).'</option>';

                            $listing_area = get_terms (
                                array(
                                    "listing_area"
                                ),
                                array(
                                    'orderby' => 'name',
                                    'order' => 'ASC',
                                    'hide_empty' => false,
                                    'parent' => 0
                                )
                            );
                            homey_hirarchical_options('listing_area', $listing_area, $listing_area_pre );
                            ?>
                            </select>

                            <?php } ?>

							
						</div>
                        <?php if( homey_option('enable_radius') ) { ?>
                        <?php $radius_show_type = homey_option('show_radius') == 0 ? "style='display: none;'" : '' ?>
                        <div <?php echo $radius_show_type; ?> class="search-type search-radius-dropdown">
                            <select  name="radius" data-size="5" class="selectpicker">
                                <option value=""><?php esc_html_e('Radius','homey');?></option>
                                <?php
                                $radius_unit = homey_option('radius_unit', 'km');
                                $selected_radius = homey_option('default_radius', '30');
                                if( isset( $_GET['radius'] ) ) {
                                    $selected_radius = $_GET['radius'];
                                }
                                $i = 0;
                                for( $i = 1; $i <= 100; $i++ ) {
                                    echo '<option '.selected( $selected_radius, $i, false).' value="'.esc_attr($i).'">'.esc_attr($i).' '.esc_attr($radius_unit).'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <?php
                        }
                       ?>
                       		<button style=" background-color: #e85d74 !important; position: absolute;right: 0;top: 0;" type="submit"></button>

	</div>
</form>
