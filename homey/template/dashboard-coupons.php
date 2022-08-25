<?php
/**
 * Template Name: Dashboard Coupons
 */
if ( !is_user_logged_in() || homey_is_renter() ) {
    wp_redirect(  home_url('/') );
}

get_header(); 

global $current_user, $post;
$dashboard_add_coupon = homey_get_template_link_dash('template/dashboard-add-coupon.php');
//print_r($dashboard_listings_coupon);
$hide_labels = homey_option('show_hide_labels');

wp_get_current_user();
$userID         = $current_user->ID;
$user_login     = $current_user->user_login;
$edit_link      = homey_get_template_link('template/dashboard-submission.php');
$coupons_page  = homey_get_template_link('template/dashboard-coupons.php');

$publish_active = $pending_active = $draft_active = $mine_active = $all_active = $disabled_active = 'btn btn-primary-outlined btn-slim';
if( isset( $_GET['status'] ) && $_GET['status'] == 'publish' ) {
    $publish_active = 'btn btn-primary btn-slim';

} elseif( isset( $_GET['status'] ) && $_GET['status'] == 'pending' ) {
    $pending_active = 'btn btn-primary btn-slim';

} elseif( isset( $_GET['status'] ) && $_GET['status'] == 'draft' ) {
    $draft_active = 'btn btn-primary btn-slim';

} elseif( isset( $_GET['status'] ) && $_GET['status'] == 'disabled' ) {
    $disabled_active = 'btn btn-primary btn-slim';

} elseif( isset( $_GET['status'] ) && $_GET['status'] == 'mine' ) {
    $mine_active = 'btn btn-primary btn-slim';

} else {
    $all_active = 'btn btn-primary btn-slim';
}

$all_link = add_query_arg( 'status', 'any', $coupons_page );
$publish_link = add_query_arg( 'status', 'publish', $coupons_page );
$pending_link = add_query_arg( 'status', 'pending', $coupons_page );
$draft_link = add_query_arg( 'status', 'draft', $coupons_page );
$disabled_link = add_query_arg( 'status', 'disabled', $coupons_page );
$mine_link = add_query_arg( 'status', 'mine', $coupons_page );
$qry_status = isset( $_GET['status'] ) ? $_GET['status'] : 'any';

$no_of_coupon   =  '9';
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
    'post_type'         =>  'coupon_id',
    
);
 if(homey_is_host() || homey_is_renter()) {
     $args['author'] = $userID;
 } else {
     if( isset( $_GET['status'] ) && $_GET['status'] == 'mine' ) {
        $args['author'] = $userID;
    }
 }

if( isset ( $_GET['keyword'] ) ) {
     $keyword = trim( $_GET['keyword'] );
     if ( ! empty( $keyword ) ) {
         $args['s'] = $keyword;

         // to search with ID
         if( is_numeric( $keyword ) ) {
             $id = abs( intval( $keyword ) );
             if( $id > 0 ) {
                 unset( $args[ 's' ] );
                 $args['post__in'] = array($keyword);
             }
         }
         // end of to search with ID
     }
 }
$args = homey_coupon_sort ( $args );

$coupon_qry = new WP_Query($args);

//  echo "<pre>";
//  print_r($coupon_qry);
//  exit;

$post_type = 'coupon_id';
$user_post_count = count_user_posts( $userID , $post_type );

$num_posts    = wp_count_posts( $post_type, 'readable' );
/*print_r($num_posts);
echo $num_posts->publish;*/
$num_post_arr = (array) $num_posts;
unset($num_post_arr['auto-draft']);
$total_posts  = array_sum($num_post_arr);
?>

<section id="body-area">

    <div class="dashboard-page-title">
        <h1><?php echo esc_html__(the_title('', '', false), 'homey'); ?></h1>
    </div><!-- .dashboard-page-title -->

    <?php get_template_part('template-parts/dashboard/side-menu'); ?>

    <div class="user-dashboard-right dashboard-without-sidebar">
        <div class="dashboard-content-area">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div id="coupons_module_section" class="dashboard-area">
                            <div class="block">
                                <div class="block-title">
                                    <div class="block-left">
                                        <h2 class="title"><?php echo esc_attr($homey_local['manage_label']); ?></h2>
                                        <div class="mt-10">
                                            <?php
                                                if(homey_is_admin()) {
                                                    echo '<a class="'.esc_attr($all_active).'" href="'.esc_url($all_link).'">'.esc_html__('All', 'homey').' ('.$total_posts.')</a> ';

                                                    echo '<a class="'.$mine_active.'" href="'.esc_url($mine_link).'">'.esc_html__('Mine', 'homey').' ('.$user_post_count.')</a> '; 

                                                    echo '<a class="'.$mine_active.'" href="'.$dashboard_add_coupon.'">'.esc_html__('Add Coupon', 'homey').'</a>'; 
                                                } else {
                                                    echo '<a class="'.esc_attr($all_active).'" href="'.esc_url($all_link).'">'.esc_html__('All', 'homey').'</a> ';
                                                }

                                                foreach ($num_posts as $key => $value) {
                                                    if($value != 0) {
                                                        if($key == 'publish' || $key == 'pending' || $key == 'draft' || $key == 'disabled') {
                                                            if($key == 'publish') {
                                                                $key_text = esc_html__('Published', 'homey');
                                                                $b_class = $publish_active;
                                                                $b_link = $publish_link;

                                                            } elseif($key == 'pending') {
                                                                $key_text = esc_html__('Pending', 'homey');
                                                                $b_class = $pending_active;
                                                                $b_link = $pending_link;

                                                            } elseif($key == 'draft') {
                                                                $key_text = esc_html__('Draft', 'homey');
                                                                $b_class = $draft_active;
                                                                $b_link = $draft_link;
                                                            } elseif($key == 'disabled') {
                                                                $key_text = esc_html__('Disabled', 'homey');
                                                                $b_class = $disabled_active;
                                                                $b_link = $disabled_link;
                                                            }
                                                            if(homey_is_admin()) {
                                                                echo '<a class="'.$b_class.'" href="'.esc_url($b_link).'">'.$key_text.' ('.$value.')</a> ';
                                                            } else {
                                                                echo '<a class="'.$b_class.'" href="'.esc_url($b_link).'">'.$key_text.'</a> ';
                                                            }
                                                        }
                                                    }
                                                }
                                            ?>

                                        </div>
                                        
                                    </div>
                                    <div class="block-right">
                                        <div class="dashboard-form-inline">
                                            <form class="form-inline">
                                                <div class="form-group">
                                                    <input name="keyword" type="text" class="form-control" value="<?php echo isset($_GET['keyword']) ? $_GET['keyword'] : '';?>" placeholder="<?php echo esc_html__('Search Listing', 'homey'); ?>">
                                                </div>
                                                <button type="submit" class="btn btn-primary btn-search-icon"><i class="fa fa-search" aria-hidden="true"></i></button>
                                            </form>
                                        </div>
                                    </div>
                               
                               
                                </div>
                            
                                <?php 
                                if($coupon_qry->have_posts()): ?>
                                    <div class="table-block dashboard-coupon-table dashboard-table">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    
                                                    <th><?php echo esc_attr($homey_local['owner_label']); ?></th>
                                                    <th><?php echo esc_html__('Coupen Name', 'homey'); ?></th>
                                                    <th><?php echo esc_html__('Detail', 'homey'); ?></th>
                                                    <th><?php echo esc_html__('Coupon(%)',''); ?></th>
                                                    <th><?php echo esc_html__('Coupen Code', 'homey'); ?></th>
                                                    <th><?php echo esc_html__('Start Date', 'homey'); ?></th>
                                                    <th><?php echo esc_html__('Expery Date', 'homey'); ?></th>
                                                    <th><?php echo esc_html__('Id', 'homey'); ?></th>
                                                    <th><?php echo esc_html__('Apply coupon'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody id="module_coupons">
                                                <?php 
                                                while ($coupon_qry->have_posts()): $coupon_qry->the_post();
                                                     get_template_part('template-parts/dashboard/coupon-item');
                                                    
                                                endwhile;
                                                ?>
                                            </tbody>
                                            
                                            </div>
                                        </table>
                                    </div>
                                    
                                <?php
                                else:
                                    echo '<div class="block-body">';
                                    echo esc_html__('coupon_dont_have', 'homey');  
                                    echo '</div>';      
                                endif; 
                                ?>
                            </div><!-- .block -->

                            <?php homey_pagination( $coupon_qry->max_num_pages, $range = 2 ); ?>

                        </div><!-- .dashboard-area -->
                    </div><!-- col-lg-12 col-md-12 col-sm-12 -->
                </div>
            </div><!-- .container-fluid -->
        </div><!-- .dashboard-content-area --> 
    </div><!-- .user-dashboard-right -->

</section><!-- #body-area -->



<?php get_footer();?>
