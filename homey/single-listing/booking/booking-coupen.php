<div class="coupen-for-booking">
 <?php
 
 global $post;
   $prefix = 'homey_';
   $price_separator = homey_option('currency_separator');
   $coupens = get_post_meta($post->ID, $prefix.'coupens',true);
   $coupen_code = get_post_meta($post->ID, $prefix.'coupens_code',true);
   $expary_date = get_post_meta($post->ID, $prefix.'expary_date',true);
   //$current_date=date(get_option('date_format'));
   $current_date  = strtotime(date(get_option('date_format')));
   $expiry_date  = strtotime($expary_date); 
   
    ?>
  <div class="less-coupen-deal">
    <form method="post" action="" class="form-group form-bottom-coupen">
      <input type="text" name="coupen" placeholder="enter coupen code"  class="form-control coupens-deals">
      <button type="submit" name="apply" class="btn btn-primary">APPLY</button>
    </form>
  </div>
</div>  
<?php

if(isset($_POST['apply']))
{
  if($current_date<=$expiry_date && $_POST['coupen']==$coupen_code)
   {
   echo 
   '<div class="valid-mesage">
       coupen is valid
   </div>';
   }
   else
   {
    
    echo 
    '<div class="expery-message">
       Date is expary or coupen is invalid
    </div>';
   }
}    

?>
