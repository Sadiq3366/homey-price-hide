
<?php

global $homey_local;
$terms_conditions = homey_option('login_terms_condition');
$register_image = homey_option('register_image', false, 'url' );
$register_text = esc_html__(homey_option('register_text'), 'homey');
$facebook_login = homey_option('facebook_login');
$google_login = homey_option('google_login');
$show_roles = homey_option('show_roles');
$enable_password = homey_option('enable_password');
$enable_forms_gdpr = homey_option('enable_forms_gdpr');

//I agree with your <a href="http://your-website.com/privacy-policy">Privacy Policy</a>

$forms_gdpr_text = explode('<a ', homey_option('forms_gdpr_text'));
$forms_gdpr_text_string = esc_html__($forms_gdpr_text[0], 'homey');

$forms_gdpr_link = '';
if(isset($forms_gdpr_text[1])){
    $forms_gdpr_link = explode('</a>', $forms_gdpr_text[1]);
    $forms_gdpr_link = '<a '.$forms_gdpr_link[0].'</a>';
}


?>
<div class="modal fade custom-modal-login" id="modal-registers" tabindex="-1" role="dialog">
    <div class="modal-dialog clearfix" role="document">
        <?php if(!empty($register_image)) { ?>
        <div class="modal-body-left pull-left" style="background-image: url(<?php echo esc_url($register_image); ?>); background-size: cover; background-repeat: no-repeat; background-position: 50% 50%;">
            <div class="login-register-title">
                <?php echo esc_attr($register_text); ?>
            </div>
        </div>
        <?php } ?>

        <div class="modal-body-right pull-right">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php echo esc_html__('Subscription:', 'homey'); ?></h4>
                </div>
                <form method="post" action="#">
                    <div class="form-group form-discription">
                        <input name="username" type="text" class="form-control email-input-1" placeholder="<?php esc_attr_e('Subscription','homey'); ?>" />
                    </div>
                    <div class="btn-submit-clas">
                         <button type="submit" name="submit" class="btn btn-primary"><?php echo esc_html__('Submit', 'homey'); ?></button>
                    </div>    
                </form>    
            </div><!-- /.modal-dialog -->
        </div>
    </div>
</div><!-- /.modal -->

<?php
if(isset($_POST['submit']))
{
    echo "this is my pakistan";
  
} 
?>