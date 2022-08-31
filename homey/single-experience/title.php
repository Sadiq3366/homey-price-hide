<?php
global $post, $homey_prefix, $experience_author;
$address = homey_get_experience_data('experience_address');

$is_superhost = $experience_author['is_superhost'];

$rating = homey_option('rating');
$total_rating = get_post_meta( $post->ID, 'experience_total_rating', true );
?>
<div class="title-section">
    <div class="block block-top-title">
        <div class="block-body">
            <?php get_template_part('template-parts/breadcrumb'); ?>
            <h1 class="experience-title">
                <?php the_title(); ?> <?php homey_experience_featured(get_the_ID()); ?>    
            </h1>

            <?php if(!empty($address)) { ?>
            <address><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc_attr($address); ?></address>
            <?php } ?>

            <div class="host-avatar-wrap avatar">
                <?php if($is_superhost) { ?>
                <span class="super-host-icon">
                    <i class="fa fa-bookmark"></i>
                </span>
                <?php } ?>
                <?php echo ''.$experience_author['photo']; ?>
            </div>

            <?php if($rating && ($total_rating != '' && $total_rating != 0 ) ) { ?>
            <div class="list-inline rating hidden-xs">
                <?php echo homey_get_exp_review_stars($total_rating, true, true); ?>
            </div>
            <?php } ?>
        </div><!-- block-body -->
    </div><!-- block -->
</div><!-- title-section -->