<?php

global $post,
    $edit_link,
    $homey_local,
    $homey_prefix,
    $prop_address,
    $prop_featured,
    $payment_status;


$All_users = get_users(); 
 

$dashboard_users = homey_get_template_link('template/dashboard-users.php');
$edit_link  = add_query_arg('edit_users', $post_id, $edit_link);
$delete_link  = add_query_arg('users_id', $post_id, $dashboard_users);
//$property_status = get_post_status ( $post->ID );
//$check_users_status = $property_status;
$dashboard = homey_get_template_link('template/dashboard.php');


// $activity = get_post_meta($users_id, 'homey_users', true);
// //print_r($activity);
// $coupen=$activity['users_value'];
// $coupens_code=$activity['users_code'];
// $start_date=$activity['users_strt'];
// $end_date=$activity['users_end'];


//update listing with users
// if(isset($_POST['users_id']))
// {
//     $users_id=$_POST['users_id'];
//     $my_meta_listing=$_POST['test_any'];
//     foreach($my_meta_listing as $lisitngs_ids){
            
//     update_post_meta( $lisitngs_ids, 'homey_users_listing' ,$users_id); 

//     }
// }   
 
// $args = array(
    
//     'post_author' => get_current_user_id(),
//     'post_type'   => 'listing',
//   );

// $listing_users=get_posts($args);

        
// $values=get_post_meta($users_idds,'homey_users_listing',true);
// print_r($values);
//      if(!empty($values))
//      {
        
//           $argss = array(
             
//                'post__in' => array($users_idds),
               
//           );
          
//            $lisitng_data=get_posts($argss);
          

//      }
//     }
?>


<tr>
<?php foreach($All_users as $data){
            $users_id=$data->ID;
            $users_email=$data->user_email;
?>
<td data-label="<?php  echo esc_html__('ID', 'homey'); ?>">
        <?php if (!empty($users_id)) { ?>
            <?php echo $users_id ?></td>
<?php }
?>
</td>
<td data-label="<?php  echo esc_html__('Email', 'homey'); ?>">
        <?php if (!empty($users_email)) { ?>
            <?php echo $users_email ?></td>
<?php }
?>
</td>

<td data-label="<?php echo esc_attr($homey_local['actions_label']); ?>">
    <div class="custom-actions">


        <button class="btn-action myusers" onclick="openModal(<?php echo $users_id?>)"  data-placement="top"> <i class="fa fa-pencil"></i></button>

   </div>   
</tr>
<?php }?>   
        <div id="<?php echo $users_id?>" class="modal-users">

         <!-- Modal content -->
           <div class="users-model-content">
              <span class="close-users">&times;</span>
             <form method="post" action=""> 
                <div class="coupen_left_model">
                    <div class="users_lable">  
                      Coupon Name
                    </div>
                    <div class="form-group">
                      <input type="text"  class="form-control" name="users_name" value="<?php echo $users_name?>">
                    </div>
                </div>
                <div class="coupen_right_model">
                    <div class="users_lable">  
                      Coupon value(%)
                    </div>
                    <div class="form-group">
                      <input type="text"  class="form-control" name="users_value" value="<?php echo $coupen?>">
                    </div>
                </div>
                <div class="coupen_left_model">
                    <div class="users_lable">  
                      Apply Coupon on All Listings:
                    </div>
                    
                    <div>
                     <input type="checkbox" id="All-listing" name="All-listing" value="Accepted">
                     
                    </div>
                    <input type="hidden"  class="form-control" name="users_id" value="<?php echo $users_id?>">
                    <input type="hidden"  class="form-control" name="coupens_code" value="<?php echo $coupens_code?>">
                    <input type="hidden"  class="form-control" name="start_date" value="<?php echo $start_date?>">
                    <input type="hidden"  class="form-control" name="end_date" value="<?php echo $end_date?>">
                    
                </div>
                <div class="coupen_right_model">
                
                <select  multiple class="chosen-select selectpicker" name="test_any[]">
                   <option value=""></option>
                  <?php
                  foreach($listing_users as $listing_users)
                  {                                   
                   echo '<option value="'.$listing_users->ID.'">'.$listing_users->post_title.'</option>';
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
                    <div class="userslable">  
                     Coupen Applyed Listing Detail:
                    </div>    
                 <?php if(!empty($listing_users)): ?>
                    <div class="table-block dashboard-users-table dashboard-table">
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
                            <tbody id="module_users">
                                <?php 
                                foreach($values as $listing_key){
 
                                    $post_users=get_posts($listing_key);   
                                    $listing_title=$post_users->post_title; 
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
                                       <?php if (!empty($users_name)) { ?>
                                       <?php echo $users_name ?></td>
                                       <?php }
                                       ?>
                                    </td>
                                    <td data-label="<?php  echo esc_html__('Coupen name', 'homey'); ?>">
                                       <?php if (!empty($users_name)) { ?>
                                       <?php echo $users_name ?></td>
                                       <?php }
                                       ?>
                                    </td>
                                    <td data-label="<?php  echo esc_html__('Coupen name', 'homey'); ?>">
                                       <?php if (!empty($users_name)) { ?>
                                       <?php echo $users_name ?></td>
                                       <?php }
                                       ?>
                                    </td>
                                    <td data-label="<?php  echo esc_html__('Coupen name', 'homey'); ?>">
                                       <?php if (!empty($users_name)) { ?>
                                       <?php echo $users_name ?></td>
                                       <?php }
                                       ?>
                                    </td>
                                    <td data-label="<?php  echo esc_html__('Coupen name', 'homey'); ?>">
                                       <?php if (!empty($users_name)) { ?>
                                       <?php echo $users_name ?></td>
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
                        echo esc_html__('You Dont have apply users on any listings', 'homey');  
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
