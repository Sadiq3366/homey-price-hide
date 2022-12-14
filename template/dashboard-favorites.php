<?php
/**
 * Template Name: Dashboard Favorites
 */
if ( !is_user_logged_in() ) {
    wp_redirect(  home_url('/') );
}

get_header(); 

global $current_user, $post;

wp_get_current_user();
$userID         = $current_user->ID;
$user_login     = $current_user->user_login;

$fav_ids = 'homey_favorites-'.$userID;
$fav_ids = get_option( $fav_ids );

$cgl_beds = homey_option('cgl_beds');
$cgl_baths = homey_option('cgl_baths');
$cgl_guests = homey_option('cgl_guests');

$is_listing_fav_empty = $is_experience_fav_empty = 0;
?>

<section id="body-area">

    <div class="dashboard-page-title">
        <h1><?php echo $homey_local['m_favorites_label']; ?></h1>
    </div><!-- .dashboard-page-title -->

    <?php get_template_part('template-parts/dashboard/side-menu'); ?>

    <div class="user-dashboard-right dashboard-without-sidebar">
        <div class="dashboard-content-area">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="dashboard-area">
                            <div class="block">
                                <div class="block-title">
                                    <div class="block-left">
                                        <h2 class="title"><?php echo esc_attr($homey_local['manage_label']); ?></h2>
                                    </div>
                                </div>

                                <?php 
                                if(!empty($fav_ids)) {
                                    $args = array('post_type' => 'listing', 'post__in' => $fav_ids, 'numberposts' => -1, 'post_status' => array('expired', 'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash')
                                    );
                                    $myposts = get_posts($args);

                                    if(isset($myposts[0])){ ?>
                                        <div class="table-block dashboard-listing-table dashboard-table">
                                            <table class="table table-hover">
                                                <thead>
                                                <tr>
                                                    <th><?php echo esc_attr($homey_local['thumb_label']); ?></th>
                                                    <th><?php echo esc_attr($homey_local['address']); ?></th>
                                                    <th><?php echo homey_option('sn_type_label'); ?></th>
                                                    <th><?php echo esc_attr($homey_local['price_label']); ?></th>

                                                    <?php if($cgl_beds != 0) { ?>
                                                        <th><?php echo homey_option('glc_bedrooms_label');?></th>
                                                    <?php } ?>

                                                    <?php if($cgl_baths != 0) { ?>
                                                        <th><?php echo homey_option('glc_baths_label');?></th>
                                                    <?php } ?>

                                                    <?php if($cgl_guests != 0) { ?>
                                                        <th><?php echo homey_option('glc_guests_label');?></th>
                                                    <?php } ?>

                                                    <th><?php echo esc_attr($homey_local['actions_label']); ?></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php

                                                foreach ($myposts as $post) : setup_postdata($post);
                                                    get_template_part('template-parts/dashboard/favorite-item');
                                                endforeach;
                                                wp_reset_postdata();
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php
                                    }else{
                                        $is_listing_fav_empty = 1;
                                    }
                                    ?>

                                <?php
                                } else {
                                    $is_listing_fav_empty = 1;
                                }
                                ?>

                                <!--experiences table list-->
                                <?php
                                if(!empty($fav_ids)) {
                                    $args = array('post_type' => 'experience', 'post__in' => $fav_ids, 'numberposts' => -1, 'post_status' => array('expired', 'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash')
                                    );
                                    $myposts = get_posts($args);

                                    if(isset($myposts[0])){ ?>
                                        <div class="table-block dashboard-listing-table dashboard-table">
                                            <table class="table table-hover">
                                                <thead>
                                                <tr>
                                                    <th colspan="6"><?php echo esc_html__('Favorited Experiences', 'homey'); ?></th>
                                                </tr>

                                                <tr>
                                                    <th><?php echo esc_attr($homey_local['thumb_label']); ?></th>
                                                    <th><?php echo esc_attr($homey_local['address']); ?></th>
                                                    <th><?php echo homey_option('sn_type_label'); ?></th>
                                                    <th><?php echo esc_attr($homey_local['price_label']); ?></th>

                                                    <?php if($cgl_guests != 0) { ?>
                                                        <th><?php echo homey_option('glc_guests_label');?></th>
                                                    <?php } ?>

                                                    <th><?php echo esc_attr($homey_local['actions_label']); ?></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php

                                                foreach ($myposts as $post) : setup_postdata($post);
                                                    get_template_part('template-parts/dashboard/favorite-exp-item');
                                                endforeach;
                                                wp_reset_postdata();
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php
                                    }else{
                                        $is_experience_fav_empty = 1;
                                    }
                                    ?>

                                    <?php
                                } else {
                                    $is_experience_fav_empty = 1;
                                }
                                ?>
                                <!-- end of experiences table list-->
                            </div><!-- .block -->

                            <?php if ($is_listing_fav_empty == 1 && $is_experience_fav_empty == 1){
                                echo '<div class="block-body">';
                                echo esc_attr($homey_local['fav_dont_have']);
                                echo '</div>';
                            }
                            ?>
                        </div><!-- .dashboard-area -->
                    </div><!-- col-lg-12 col-md-12 col-sm-12 -->
                </div>
            </div><!-- .container-fluid -->
        </div><!-- .dashboard-content-area --> 
    </div><!-- .user-dashboard-right -->

</section><!-- #body-area -->


<?php get_footer();?>