<?php
/**
 * Template Name: Dashboard add-coupon
 */
if ( !is_user_logged_in() || homey_is_renter() ) {
    wp_redirect(  home_url('/') );
}

get_header(); 

global $current_user, $post;
$hide_labels = homey_option('show_hide_labels');

wp_get_current_user();
$userID         = $current_user->ID;
$user_login     = $current_user->user_login;
$edit_link      = homey_get_template_link('template/dashboard-submission.php');
$add_coupon_page  = homey_get_template_link('template/dashboard-add-coupon.php');

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

$all_link = add_query_arg( 'status', 'any', $add_coupon_page );
$publish_link = add_query_arg( 'status', 'publish', $add_coupon_page );
$pending_link = add_query_arg( 'status', 'pending', $add_coupon_page );
$draft_link = add_query_arg( 'status', 'draft', $add_coupon_page );
$disabled_link = add_query_arg( 'status', 'disabled', $add_coupon_page );
$mine_link = add_query_arg( 'status', 'mine', $add_coupon_page );

$qry_status = isset( $_GET['status'] ) ? $_GET['status'] : 'any';

$no_of_listing   =  '9';
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
    'post_type'         =>  'coupon',
    
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
//print_r($args);
$listing_qry = new WP_Query($args);

$post_type = 'coupon';
$user_post_count = count_user_posts( $userID , $post_type );
$num_posts    = wp_count_posts( $post_type, 'readable' );
/*print_r($num_posts);
echo $num_posts->publish;*/
$num_post_arr = (array) $num_posts;
unset($num_post_arr['auto-draft']);
$total_posts  = array_sum($num_post_arr);

$my_post = array(
    'post_title'    =>  $_POST['coupon_name'],
    'post_content'    =>  $_POST['coupon_dscrp'],
    'post_type'    =>  'coupon_id',
    'post_status'    =>  'publish'
    
  );
  //print_r($my_post);
  // Insert the post into the database
  $post_id =wp_insert_post( $my_post );
  
  //insert meta data in database
//print_r($post_id);

  $my_meta_post = array(
    'coupon_value'    =>  $_POST['coupon_value'],
    'coupon_code'    =>  $_POST['coupon_code'],
    'coupon_strt'    =>  $_POST['coupon_strt'],
    'coupon_end'    =>  $_POST['coupon_end']
    
  );
  add_post_meta( $post_id, 'homey_coupon' ,$my_meta_post); 
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
                        <div id="listings_module_section" class="dashboard-area">
                            <div class="block">
                                <div class="block-title">
                                    <div class="block-left">
                                        <h2 class="title"><?php echo esc_attr($homey_local['manage_label']); ?></h2>
                                           
                                    </div>
                                    <div class="block-right">
                                        <div class="dashboard-form-inline">
                                            <form class="form-inline">
                                                <div class="form-group">
                                                    <input name="keyword" type="text" class="form-control" value="<?php echo isset($_GET['keyword']) ? $_GET['keyword'] : '';?>" placeholder="<?php echo esc_attr__('Search listing', 'homey'); ?>">
                                                </div>
                                                <button type="submit" class="btn btn-primary btn-search-icon"><i class="fa fa-search" aria-hidden="true"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                             
                            <div class="coupon_lbel">
                            <form method="post" action="">
                                <div class="add_coupon_lft">
                                    <div class="coupon_lable">  
                                        Add coupon(%)
                                    </div>
                                   <div class="form-group">
                                     <input name="coupon_value" pattern="[0-9]+" type="text" class="form-control" placeholder="<?php echo esc_html__('Enter New Coupon', 'homey'); ?>">
                                    </div>
                                </div>
                           
                          
                                <div class="add_coupon_rt">
                                    <div class="coupon_lable">  
                                        Add coupon code
                                    </div>
                                   <div class="form-group">
                                     <input name="coupon_code" type="text" class="form-control" placeholder="<?php echo esc_html__('Enter New Coupon', 'homey'); ?>">
                                    </div>
                                </div> 
                                <div class="add_coupon_lft">
                                    <div class="coupon_lable">  
                                        Coupon Name
                                    </div>
                                   <div class="form-group">
                                     <input name="coupon_name" type="text" class="form-control" placeholder="<?php echo esc_html__('Enter New Coupon', 'homey'); ?>">
                                    </div>
                                </div>
                           
                          
                                <div class="add_coupon_rt">
                                    <div class="coupon_lable">  
                                        Discriptions
                                    </div>
                                   <div class="form-group">
                                     <input name="coupon_dscrp" type="text" class="form-control" placeholder="<?php echo esc_html__('Enter New Coupon', 'homey'); ?>">
                                    </div>
                                </div> 
                                <div class="add_coupon_lft">
                                    <div class="coupon_lable">  
                                      Start Date  
                                    </div>
                                   <div class="form-group">
                                     <input name="coupon_strt" type="date" class="form-control" placeholder="<?php echo esc_html__('Enter New Coupon', 'homey'); ?>">
                                    </div>
                                </div>
                           
                           
                                <div class="add_coupon_rt">
                                    <div class="coupon_lable">  
                                    Expery Date 
                                    </div>
                                   <div class="form-group">
                                     <input name="coupon_end" type="date" class="form-control" placeholder="<?php echo esc_html__('Enter New Coupon', 'homey'); ?>">
                                    </div>
                                </div> 
                                <div class="add_coupon_lft">
                                <button type="submit" class="btn btn-primary" > Add Coupon </i></button>

                                </div>

                            </div>

                             
 
                            </div> 
                        </div>
                    </div> 
                </div>
            </div> 
        </div>      
    </div>
                              


<?php get_footer();?>
