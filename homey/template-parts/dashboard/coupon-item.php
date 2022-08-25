<?php

global $post,
    $edit_link,
    $homey_local,
    $homey_prefix,
    $prop_address,
    $prop_featured,
    $payment_status;

$coupon_id    = get_the_ID();
$coupen_title = get_post( $coupon_id ); 
$title = $coupen_title->post_title;
$detail = $coupen_title->post_content;
$coupon_name = $title;
//$coupen_detail = get_post( $coupon_id ); 

$coupon_deatail = $detail;

$dashboard_coupons = homey_get_template_link('template/dashboard-coupons.php');
$edit_link  = add_query_arg('edit_coupon', $post_id, $edit_link);
$delete_link  = add_query_arg('coupon_id', $post_id, $dashboard_coupons);
//$property_status = get_post_status ( $post->ID );
//$check_coupon_status = $property_status;
$dashboard = homey_get_template_link('template/dashboard.php');


$activity = get_post_meta($coupon_id, 'homey_coupon', true);
//print_r($activity);
$coupen=$activity['coupon_value'];
$coupens_code=$activity['coupon_code'];
$start_date=$activity['coupon_strt'];
$end_date=$activity['coupon_end'];


//update listing with coupon
if(isset($_POST['coupon_id']))
{
    $coupons_id=$_POST['coupon_id'];
    $my_meta_listing=$_POST['test_any'];
    foreach($my_meta_listing as $lisitngs_ids){
            
    update_post_meta( $lisitngs_ids, 'homey_coupon_listing' ,$coupons_id); 

    }
}   
 
$args = array(
    
    'post_author' => get_current_user_id(),
    'post_type'   => 'listing',
  );

$listing_coupons=get_posts($args);

     foreach($listing_coupons as $coupon_key){
        $coupon_idds=$coupon_key->ID;
        
$values=get_post_meta($coupon_idds,'homey_coupon_listing',true);
print_r($values);
     if(!empty($values))
     {
        
          $argss = array(
             
               'post__in' => array($coupon_idds),
               
          );
          
           $lisitng_data=get_posts($argss);
          

     }
    }
?>


<tr>

    <td data-label="<?php echo esc_attr($homey_local['owner_label']); ?>">
        <?php echo esc_attr(get_the_author()); ?>
    </td>



    <!-- <td data-label="ID">HY01</td> -->

</td>
<td data-label="<?php  echo esc_html__('Coupen name', 'homey'); ?>">
        <?php if (!empty($coupon_name)) { ?>
            <?php echo $coupon_name ?></td>
<?php }
?>
</td>
<td data-label="<?php  echo esc_html__('Detail', 'homey'); ?>">
        <?php if (!empty($coupon_deatail)) { ?>
            <?php echo $coupon_deatail ?></td>
<?php }
?>
</td>
<td data-label="<?php echo esc_attr($homey_local['coupens']); ?>">
        <?php if (!empty($coupen)) { ?>
            <?php echo $coupen ?></td>
<?php }
?>
</td>
<td data-label="<?php echo esc_html__('Coupen Code', 'homey'); ?>">
    <?php if (!empty($coupens_code)) { ?>
        <?php echo $coupens_code ?></td>
<?php }
?>
</td>
<td data-label="<?php echo esc_html__('Start Date', 'homey'); ?>">
    <?php if (!empty($start_date)) { ?>
        <?php echo $start_date ?></td>
<?php }
?>
</td>
<td data-label="<?php echo esc_html__('Expery Date', 'homey'); ?>">
    <?php if (!empty($end_date)) { ?>
        <?php echo $end_date ?></td>
<?php }
?>
</td>


<td data-label="<?php echo homey_option('sn_id_label'); ?>"><?php echo $coupon_id; ?></td>

<td data-label="<?php echo esc_attr($homey_local['actions_label']); ?>">
    <div class="custom-actions">


        <button class="btn-action mycoupon" onclick="openModal(<?php echo $coupon_id?>)"  data-placement="top"> <i class="fa fa-pencil"></i></button>

   </div>   
        <div id="<?php echo $coupon_id?>" class="modal-coupon">

         <!-- Modal content -->
           <div class="coupon-model-content">
              <span class="close-coupon">&times;</span>
             <form method="post" action=""> 
                <div class="coupen_left_model">
                    <div class="coupon_lable">  
                      Coupon Name
                    </div>
                    <div class="form-group">
                      <input type="text"  class="form-control" name="coupon_name" value="<?php echo $coupon_name?>">
                    </div>
                </div>
                <div class="coupen_right_model">
                    <div class="coupon_lable">  
                      Coupon value(%)
                    </div>
                    <div class="form-group">
                      <input type="text"  class="form-control" name="coupon_value" value="<?php echo $coupen?>">
                    </div>
                </div>
                <div class="coupen_left_model">
                    <div class="coupon_lable">  
                      Apply Coupon on All Listings:
                    </div>
                    
                    <div>
                     <input type="checkbox" id="All-listing" name="All-listing" value="Accepted">
                     
                    </div>
                    <input type="hidden"  class="form-control" name="coupon_id" value="<?php echo $coupon_id?>">
                    <input type="hidden"  class="form-control" name="coupens_code" value="<?php echo $coupens_code?>">
                    <input type="hidden"  class="form-control" name="start_date" value="<?php echo $start_date?>">
                    <input type="hidden"  class="form-control" name="end_date" value="<?php echo $end_date?>">
                    
                </div>
                <div class="coupen_right_model">
                
                <select  multiple class="chosen-select selectpicker" name="test_any[]">
                   <option value=""></option>
                  <?php
                  foreach($listing_coupons as $listing_coupon)
                  {                                   
                   echo '<option value="'.$listing_coupon->ID.'">'.$listing_coupon->post_title.'</option>';
                  }
                   ?>
                </select>
 

                </div>      
                <div class="form-buttn">
                     <button type="submit" class="btn btn-primary" > Add Coupon </i></button>
                     
                 </div>   
            </form>
                <div class="this">
                    <!-- table -->
                    <div class="couponlable">  
                     Coupen Applyed Listing Detail:
                    </div>    
                 <?php if(!empty($listing_coupon)): ?>
                    <div class="table-block dashboard-coupon-table dashboard-table">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><?php echo esc_html__('Id', 'homey'); ?></th>
                                    <th><?php echo esc_html__('Title', 'homey'); ?></th>
                                    <th><?php echo esc_html__('Coupen Name', 'homey'); ?></th>                       
                                    <th><?php echo esc_html__('Coupon(%)',''); ?></th>
                                    <th><?php echo esc_html__('Coupen Code', 'homey'); ?></th>
                                    <th><?php echo esc_html__('Start Date', 'homey'); ?></th>
                                    <th><?php echo esc_html__('Expery Date', 'homey'); ?></th>
                                    
                                    
                                </tr>
                            </thead>
                            <tbody id="module_coupons">
                                <?php 
                                foreach($values as $listing_key){
 
                                    $post_coupon=get_posts($listing_key);   
                                    $listing_title=$post_coupon->post_title; 
                                 ?>
                                    <td data-label="<?php  echo esc_html__('Lisitng id', 'homey'); ?>">
                                       <?php if (!empty($listing_key)) { ?>
                                       <?php echo $listing_key ?></td>
                                       <?php }
                                       ?>
                                    </td>
                                    <td data-label="<?php  echo esc_html__('Listing Title', 'homey'); ?>">
                                       <?php if (!empty($listing_title)) { ?>
                                       <?php echo $listing_title ?></td>
                                       <?php }
                                       ?>
                                    </td>
                                    <td data-label="<?php  echo esc_html__('Coupen name', 'homey'); ?>">
                                       <?php if (!empty($coupon_name)) { ?>
                                       <?php echo $coupon_name ?></td>
                                       <?php }
                                       ?>
                                    </td>
                                    <td data-label="<?php  echo esc_html__('Coupen name', 'homey'); ?>">
                                       <?php if (!empty($coupon_name)) { ?>
                                       <?php echo $coupon_name ?></td>
                                       <?php }
                                       ?>
                                    </td>
                                    <td data-label="<?php  echo esc_html__('Coupen name', 'homey'); ?>">
                                       <?php if (!empty($coupon_name)) { ?>
                                       <?php echo $coupon_name ?></td>
                                       <?php }
                                       ?>
                                    </td>
                                    <td data-label="<?php  echo esc_html__('Coupen name', 'homey'); ?>">
                                       <?php if (!empty($coupon_name)) { ?>
                                       <?php echo $coupon_name ?></td>
                                       <?php }
                                       ?>
                                    </td>
                                    <td data-label="<?php  echo esc_html__('Coupen name', 'homey'); ?>">
                                       <?php if (!empty($coupon_name)) { ?>
                                       <?php echo $coupon_name ?></td>
                                       <?php }
                                       ?>
                                    </td>
                           <?php } 
                                ?>
                            </tbody>
                            
                            </div>
                        </table>
                    </div>
                  <?php 
                      else:
                         echo '<div class="blockbody">';
                        echo esc_html__('You Dont have apply coupon on any listings', 'homey');  
                        echo '</div>';      
                     endif; 
                  ?>  

                    <!-- end table -->
                </div>
            </div>
        </div>    
    </div>
  </div>
 </td>
</tr>
