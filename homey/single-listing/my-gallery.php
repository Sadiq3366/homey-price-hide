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
                    <!--<a data-lazy="<?php echo esc_url($image['full_url']);?>" href="<?php echo esc_url($image['full_url']);?>" class="swipebox">-->
                    <a onclick="window.open(window.location.href, '_blank');" class="swipebox">
                        <img data-fancy-image-index="<?php echo $i; ?>" class="img-responsive fanboxTopGallery-item" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                    </a>
                </div>
            <?php $i++; 
        } ?>
        </div>
        </div><!-- top-gallery-section -->
<?php //fancybox_gallery_html($listing_images, 'fanboxTopGallery');//hidden images for gallery fancybox 3 ?>
<?php } ?>
