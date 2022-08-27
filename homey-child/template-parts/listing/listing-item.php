<?php
global $post, $homey_prefix, $homey_local;
$listing_images = get_post_meta( get_the_ID(), $homey_prefix.'listing_images', false );
$address        = get_post_meta( get_the_ID(), $homey_prefix.'listing_address', true );
$bedrooms       = get_post_meta( get_the_ID(), $homey_prefix.'listing_bedrooms', true );
$guests         = get_post_meta( get_the_ID(), $homey_prefix.'guests', true );
$beds           = get_post_meta( get_the_ID(), $homey_prefix.'beds', true );
$baths          = get_post_meta( get_the_ID(), $homey_prefix.'baths', true );
$night_price    = get_post_meta( get_the_ID(), $homey_prefix.'night_price', true );
$price_no       = get_post_meta( get_the_ID(), $homey_prefix.'yes_no', true ); 

$listing_author = homey_get_author();
$enable_host = homey_option('enable_host');
$compare_favorite = homey_option('compare_favorite');

if(isset($args['arrive']) && isset($args['depart'])){
    $listing_price = homey_calculate_booking_cost_ajax_nightly(get_the_ID(), $args['arrive'], $args['depart'], $args['guests'], null, 1);
}else{
    $listing_price = homey_get_price();

}


$cgl_meta = homey_option('cgl_meta');
$cgl_beds = homey_option('cgl_beds');
$cgl_baths = homey_option('cgl_baths');
$cgl_guests = homey_option('cgl_guests');
$cgl_types = homey_option('cgl_types');
$rating = homey_option('rating');
$total_rating = get_post_meta( get_the_ID(), 'listing_total_rating', true );
    
$bedrooms_icon = homey_option('lgc_bedroom_icon'); 
$bathroom_icon = homey_option('lgc_bathroom_icon'); 
$guests_icon = homey_option('lgc_guests_icon');
$price_separator = homey_option('currency_separator');

if(!empty($bedrooms_icon)) {
    $bedrooms_icon = '<i class="'.esc_attr($bedrooms_icon).'"></i>';
}
if(!empty($bathroom_icon)) {
    $bathroom_icon = '<i class="'.esc_attr($bathroom_icon).'"></i>';
}
if(!empty($guests_icon)) {
    $guests_icon = '<i class="'.esc_attr($guests_icon).'"></i>';
}
$homey_permalink = homey_listing_permalink();
?>
<div class="item-wrap infobox_trigger homey-matchHeight"  data-id="<?php echo $post->ID; ?>">
    <div class="media property-item">
        <div class="media-left">
            <div class="item-media item-media-thumb">
                
                <?php homey_listing_featured(get_the_ID()); ?>

                <!--<a class="hover-effect" href="<?php echo esc_url($homey_permalink); ?>">-->
                <?php
                if( has_post_thumbnail( $post->ID ) ) {
                     get_template_part('single-listing/my-gallery'); 
                    //the_post_thumbnail( 'homey-listing-thumb',  array('class' => 'img-responsive' ) );
                }else{
                    homey_image_placeholder( 'homey-listing-thumb' );
                }
                ?>
                <!--</a>-->

                <?php 
                
                if($price_no=='no')
                {?>
                    <div class="item-media-price">
                        <span class="item-price sa-listing-item-price">
                            <h3>On Request</h3>
                        </span>
                    </div>
                       
                <?php }

               else if(!empty($listing_price)) { ?>
                <div class="item-media-price">
                    <span class="item-price sa-listing-item-price">
                        <?php 
                        $number_of_nights = isset($args['sa_nights_in_diff']) ? $args['sa_nights_in_diff'] : false;
                        $is_show_from = $number_of_nights > 0 ? false : true;
                         echo homey_formatted_price($listing_price, false, true, $is_show_from); ?><sub><?php echo esc_attr($price_separator); ?><?php echo homey_get_price_label($number_of_nights, $number_of_nights);?></sub>
                    </span>
                </div>
                <?php } ?>

                <?php if($enable_host) { ?>
                <div class="item-user-image">
                    <?php echo ''.$listing_author['photo']; ?>
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="media-body item-body clearfix">
            <div class="item-title-head table-block">
                <div class="title-head-left">
                    <h2 class="title"><a target="_blank" href="<?php echo esc_url($homey_permalink); ?>">
                    <?php the_title(); ?></a></h2>
                    <?php 
                    if(!empty($address)) {
                        $address_tax_separator = ''; $city_tax_html ='';
                        if(!empty(trim(homey_get_taxonomy_title( get_the_ID(), 'listing_country' ))) && !empty(trim(homey_get_taxonomy_title( get_the_ID(), 'listing_city' )))){
                            $address_tax_separator = ' > ';
                        }

                        if(!empty(trim(homey_get_taxonomy_title( get_the_ID(), 'listing_city' )))){
                            $city_tax_html = '<a href="'.esc_url(homey_get_taxonomy_meta_link( get_the_ID(), 'listing_city' )).'">'.homey_get_taxonomy_title( get_the_ID(), 'listing_city' ).'</a>';
                        }
                        // echo '<address class="item-address">'.esc_attr($address).'</address>';
                        echo '<address class="item-address sa-item-address"><a href="'.esc_url(homey_get_taxonomy_meta_link( get_the_ID(), 'listing_country' )).'">'.homey_get_taxonomy_title( get_the_ID(), 'listing_country' ).'</a> '.$address_tax_separator.$city_tax_html.'</address>';
                        
                    }
                    ?>
                </div>
            </div>

            <?php if($cgl_meta != 0) { ?>
            <ul class="item-amenities">
                
                <?php if($cgl_beds != 0 && $bedrooms != '') { ?>
                <li>
                    <?php echo ''.$bedrooms_icon; ?>
                    <span class="total-beds"><?php echo esc_attr($bedrooms); ?></span> <span class="item-label"><?php echo esc_attr(homey_option('glc_bedrooms_label'));?></span>
                </li>
                <?php } ?>

                <?php if($cgl_baths != 0 && $baths != '') { ?>
                <li>
                    <?php echo ''.$bathroom_icon; ?>
                    <span class="total-baths"><?php echo esc_attr($baths); ?></span> <span class="item-label"><?php echo esc_attr(homey_option('glc_baths_label'));?></span>
                </li>
                <?php } ?>

                <?php if($cgl_guests!= 0 && $guests != '') { ?>
                <li>
                    <?php echo ''.$guests_icon; ?>
                    <span class="total-guests"><?php echo esc_attr($guests); ?></span> <span class="item-label"><?php echo esc_attr(homey_option('glc_guests_label'));?></span>
                </li>
                <?php } ?>

                <?php if($cgl_types != 0) { ?>
                <li class="item-type"><?php echo homey_taxonomy_simple('listing_type'); ?></li>
                <?php } ?>
            </ul>
            <?php } ?>

            <?php if($enable_host) { ?>
            <div class="item-user-image list-item-hidden">
                    <?php echo ''.$listing_author['photo']; ?>
                    <span class="item-user-info"><?php echo esc_attr($homey_local['hosted_by']);?><br>
                    <?php echo esc_attr($listing_author['name']); ?></span>
            </div>
            <?php } ?>

            <div class="item-footer">

                <?php if($compare_favorite) { ?>
                <div class="footer-right">
                    <div class="item-tools">
                        <div class="btn-group dropup">
                            <?php get_template_part('template-parts/listing/compare-fav'); ?>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <?php 
                if($rating && ($total_rating != '' && $total_rating != 0 ) ) { ?>
                <div class="footer-left">
                    <div class="stars">
                        <ul class="list-inline rating">
                            <?php echo homey_get_review_stars($total_rating, false, true); ?>
                        </ul>
                    </div>
                </div>
                <?php } ?>

            </div>
        </div>
    </div>
</div><!-- .item-wrap -->
