<?php
global $post;
$size = 'homey-listing-thumb';
$listing_images = rwmb_meta( 'homey_listing_images', 'type=plupload_image&size='.$size, $post->ID );
$i = 0;

if(!empty($listing_images)) {
    ?>
 <div class="top-gallery-section">
        <div class="header-slider">
            <?php foreach( $listing_images as $image ) { ?>
                <div>
                    <?php
                    $paramsForUrl =array('arrive' => $_REQUEST['arrive'],
                        'depart' => $_REQUEST['depart'],
                        'guest' => $_REQUEST['guest'],
                        'keyword' => $_REQUEST['keyword'],
                        'pets' => $_REQUEST['pets'],
                        'bedrooms' => $_REQUEST['bedrooms'],
                        'rooms' => $_REQUEST['rooms'],
                        'room_size' => $_REQUEST['room_size'],
                        'search_country' => $_REQUEST['search_country'],
                        'search_city' => $_REQUEST['search_city'],
                        'search_area' => $_REQUEST['search_area'],
                        'min-price' => $_REQUEST['min-price'],
                        'max-price' => $_REQUEST['max-price'],
                        'country' => $_REQUEST['country'],
                        'city' => $_REQUEST['city'],
                        'area' => $_REQUEST['area']
                    )
                                        ?>
                    <!--<a data-lazy="<?php echo esc_url($image['full_url']);?>" href="<?php echo esc_url($image['full_url']);?>" class="swipebox">-->
                    <a onclick="window.open('<?php echo add_query_arg($paramsForUrl, homey_listing_permalink()); ?>', '_blank');" class="swipebox">
                        <img data-fancy-image-index="<?php echo $i; ?>" class="img-responsive fanboxTopGallery-item" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                    </a>
                </div>
            <?php $i++; 
        } ?>
        </div>
        </div><!-- top-gallery-section -->
<?php //fancybox_gallery_html($listing_images, 'fanboxTopGallery');//hidden images for gallery fancybox 3 ?>
<?php } ?>
