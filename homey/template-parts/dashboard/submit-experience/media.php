<?php
global $post, $hide_fields, $homey_local;
$hide_fields_for_experience = homey_option('experience_add_hide_fields');

$layout_order = homey_option('experience_form_sections');
$layout_order = $layout_order['enabled'];
$i = 0;
$style = 'visibility: hidden; height: 0;';
if ($layout_order) { 
    foreach ($layout_order as $key=>$value) {
        $i++;
        if($i == 2 && $key == 'media') {
            $style = 'visibility: visible;';
        }
    }
}
?>
<div class="form-step form-step-gal1" style="<?php echo esc_attr($style); ?>">
    <!--step information-->
    <div class="block">
        <div class="block-title">
            <div class="block-left">
                <h2 class="title"><?php echo esc_html(homey_option('experience_ad_section_media')); ?></h2>
            </div><!-- block-left -->
        </div>
        <div class="block-body">
            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <div class="upload-property-media">
                        <div id="homey_gallery_dragDrop" class="media-drag-drop">
                            <div class="upload-icon">
                                <i class="fa fa-picture-o" aria-hidden="true"></i>
                            </div>
                            <h4>
                                <?php echo homey_option('experience_ad_drag_drop_img'); ?><br>
                                <span><?php echo esc_attr(homey_option('experience_ad_image_size_text')); ?></span>
                            </h4>
                            <button id="select_gallery_images" href="javascript:;" class="btn btn-secondary"><i class="fa fa-cloud-upload"></i> <?php echo esc_attr(homey_option('experience_ad_upload_btn')); ?></button>
                        </div>
                        <div id="plupload-container"></div>
                        <div id="homey_errors"></div>

                        <div class="upload-media-gallery">
                            <div id="homey_gallery_container" class="row">
                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="upload-progress-images"></div>

    <?php if($hide_fields_for_experience['experience_video_url'] != 1) { ?>
    <div class="block">
        <div class="block-title">
            <div class="block-left">
                <h2 class="title"><?php echo esc_attr(homey_option('experience_ad_video_heading')); ?></h2>
            </div>
        </div>
        <div class="block-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="experience_video_url"><?php echo esc_attr(homey_option('experience_ad_video_url')); ?></label>
                        <input type="text" class="form-control" name="experience_video_url" id="video_url" placeholder="<?php echo esc_attr(homey_option('experience_ad_video_placeholder')); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    
</div>