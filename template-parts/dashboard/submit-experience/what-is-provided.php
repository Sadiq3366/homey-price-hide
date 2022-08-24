<?php global $homey_local; ?>
<div class="form-step">
    <div class="block">
        <div class="block-title">
            <div class="block-left">
                <h2 class="title"><?php echo esc_html__("I will provide", "homey"); ?></h2>
            </div><!-- block-left -->
            <div class="block-right">
                <label class="control control--checkbox margin-0">
                    <input type="checkbox" name="nothing_provided_btn" class="nothing_provided_btn" id="nothing_provided_btn">
                    <span class="contro-text"><?php echo esc_html__("Nothing to provide", "homey"); ?></span>
                    <span class="control__indicator"></span>
                </label>
            </div><!-- block-right -->
        </div>
    </div>

    <div class="block what_is_provided">
        <div class="block-title">
            <div class="block-left">
                <h2 class="title"><?php echo esc_html__("Add an item", "homey"); ?></h2>
            </div><!-- block-left -->
        </div>
        <div class="block-body">
            <div class="more_what_to_provided_main" id="more_what_to_provided_main">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="what_to_provided0"><?php echo esc_html__(esc_attr(homey_option('experience_ad_acc_what_provide_name')), 'homey'); //. homey_req('experience_what_provides'); ?> </label>
                            <input id="what_to_provided0" type="text" name="what_to_provided[0][name]" class="form-control" placeholder="<?php echo esc_html__(esc_attr(homey_option('experience_ad_acc_what_provide_name_plac')), 'homey');?>" />
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label><?php echo esc_html__("Description", "homey"); ?></label>
                            <textarea name="what_to_provided[0][desc]" class="form-control" rows="3" placeholder="<?php echo esc_html__("Type description here.", "homey"); ?>"></textarea>
                        </div>
                    </div>
                </div>
                <!--<div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <button type="button" data-remove="0" class="remove-what-to-provided btn btn-primary btn-slim"><?php esc_html_e('Delete', 'homey'); ?></button>
                    </div>
                </div>-->
            </div>
            <div class="row">
                <div class="col-sm-12 col-xs-12 text-right">
                    <button type="button" id="add_more_what_will_provided" data-increment="0" class="btn btn-primary btn-slim"><i class="fa fa-plus"></i> <?php echo esc_html__('Add More', 'homey'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="block">
        <div class="block-title">
            <div class="block-left">
                <h2 class="title"><?php echo esc_html__("Guests have to bring with them", "homey"); ?></h2>
            </div><!-- block-left -->
            <div class="block-right">
                <label class="control control--checkbox margin-0">
                    <input type="checkbox" name="nothing_bring_btn" class="nothing_bring_btn" id="nothing_bring_btn">
                    <span class="contro-text"><?php echo esc_html__("Nothing to bring", "homey"); ?></span>
                    <span class="control__indicator"></span>
                </label>
            </div><!-- block-right -->
        </div>
    </div>

    <div class="block what_to_bring">
        <div class="block-title">
            <div class="block-left">
                <h2 class="title"><?php echo esc_html__("Add an item", "homey"); ?></h2>
            </div><!-- block-left -->
        </div>
        <div class="block-body">
            <div class="more_what_to_bring_main" id="more_what_to_bring_main">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="experience_ad_what_bring_text"><?php echo esc_html__(esc_attr(homey_option('experience_what_bring_name')), 'homey'); //. homey_req('experience_what_bring'); ?> </label>
                            <textarea id="what_to_bring_name0" type="text" name="what_to_bring[0][name]" class="form-control" placeholder="<?php echo esc_html__(esc_attr(homey_option('experience_what_bring_name_plac')), 'homey'); ?>"></textarea>
                        </div>

                    </div>
                    <!--<div class="col-sm-12">
                        <div class="form-group">
                            <label><?php echo esc_html__("Description", "homey"); ?></label>
                            <textarea name="what_to_bring[0][desc]" class="form-control" rows="3" placeholder="<?php echo esc_html__("Type description here.", "homey"); ?>"></textarea>
                        </div>
                    </div>-->
                </div>
               <!-- <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <button type="button" data-remove="0" class="remove-what-to-bring btn btn-primary btn-slim"><?php esc_html_e('Delete', 'homey'); ?></button>
                    </div>
                </div>-->
            </div>
            <div class="row">
                <div class="col-sm-12 col-xs-12 text-right">
                    <button type="button" id="add_more_what_will_bring" data-increment="0" class="btn btn-primary btn-slim"><i class="fa fa-plus"></i> <?php echo esc_html__('Add More', 'homey'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
