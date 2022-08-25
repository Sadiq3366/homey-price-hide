<?php
global $post;
$size = 'homey-listing-large';
$listing_images = rwmb_meta( 'homey_listing_images', 'type=plupload_image&size='.$size, $post->ID );
$i = 0;

if(!empty($listing_images)) {
    ?>
 <div class="top-gallery-section">
        <div class="header-slider">
            <?php foreach( $listing_images as $image ) { ?>
                <div>
                    <?php
                    if( isset($_REQUEST) && !empty($_REQUEST) ) // && !isset($_REQUEST['sortby'])
                    {
                        $paramsForUrl =array(
                            'arrive' => isset($_REQUEST['arrive']) ? $_REQUEST['arrive'] : '',
                            'depart' => isset($_REQUEST['depart']) ? $_REQUEST['depart'] : '',
                            'guest' => isset($_REQUEST['guest']) ? $_REQUEST['guest'] : '',
                            'keyword' => isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : '',
                            'pets' => isset($_REQUEST['pets']) ? $_REQUEST['pets'] : '',
                            'bedrooms' => isset($_REQUEST['bedrooms']) ? $_REQUEST['bedrooms'] : '',
                            'rooms' => isset($_REQUEST['rooms']) ? $_REQUEST['rooms'] : '',
                            'room_size' => isset($_REQUEST['room_size']) ? $_REQUEST['room_size'] : '',
                            'search_country' => isset($_REQUEST['search_country']) ? $_REQUEST['search_country'] : '',
                            'search_city' => isset($_REQUEST['search_city']) ? $_REQUEST['search_city'] : '',
                            'search_area' => isset($_REQUEST['search_area']) ? $_REQUEST['search_area'] : '',
                            'min-price' => isset($_REQUEST['min-price']) ? $_REQUEST['min-price'] : '',
                            'max-price' => isset($_REQUEST['max-price']) ? $_REQUEST['max-price'] : '',
                            'country' => isset($_REQUEST['country']) ? $_REQUEST['country'] : '',
                            'city' => isset($_REQUEST['city']) ? $_REQUEST['city'] : '',
                            'area' => isset($_REQUEST['area']) ? $_REQUEST['area'] : ''
                        );
                    }
                    ?>
                    <!--<a data-lazy="<?php echo esc_url($image['full_url']);?>" href="<?php echo esc_url($image['full_url']);?>" class="swipebox">-->
                    <a onclick="window.open('<?php echo add_query_arg($paramsForUrl, homey_listing_permalink()); ?>', '_blank');" class="swipebox">
                        <img data-fancy-image-index="<?php echo $i; ?>" class="img-responsive fanboxTopGallery-item" data-lazy="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                    </a>
                </div>
            <?php $i++; 
        } ?>
        </div>
        </div><!-- top-gallery-section -->
<?php //fancybox_gallery_html($listing_images, 'fanboxTopGallery');//hidden images for gallery fancybox 3 ?>
<?php } ?>
