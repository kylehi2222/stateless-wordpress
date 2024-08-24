<?php

if(PeepSo::is_admin() && $reported) {


    $PeepSoActivity = PeepSoActivity::get_instance();
    $PeepSoUser= PeepSoUser::get_instance($post_author);
    $PeepSoPrivacy	= PeepSoPrivacy::get_instance();

    $nsfw = PeepSo::get_option_new('nsfw') ? $nsfw : FALSE;
    $pinned = PeepSo::get_option_new('pinned_posts_enable') ? $pinned : FALSE;

    $unpublished = 'pending' == $post_status;
    ?>

    <div class="ps-post ps-js-activity <?php echo (TRUE == $pinned) ? 'ps-post--pinned ps-js-activity-pinned' : ''?> <?php echo (TRUE == $unpublished) ? 'ps-post--unpublished ps-js-activity-unpublished' : ''?>   ps-js-activity--<?php echo $act_id; ?> <?php do_action('peepso_action_post_classes');?>"
         data-id="<?php echo $act_id; ?>" data-post-id="<?php echo $ID; ?>" data-author="<?php echo $post_author ?>"
         data-module-id="<?php echo $act_module_id ?>" ps-data-pinned="<?php echo __('Pinned', 'peepso-core');?>">

        <?php if($reported) PeepSoTemplate::exec_template('activity','post-reports', ['post_id'=>$ID, 'module_id'=>$act_module_id, 'type'=>'post', 'reported'=>$reported, 'reports'=>$reports,'context'=>$PeepSoActivity->post_link(FALSE),'unpublished'=>$unpublished]); ?>

        <div class="ps-post__header ps-js-post-header" data-hide-header="0">
            <a class="ps-avatar ps-avatar--post" href="<?php echo $PeepSoUser->get_profileurl(); ?>">
                <img data-author="<?php echo $post_author; ?>" src="<?php echo $PeepSoUser->get_avatar();?>" alt="<?php echo $PeepSoUser->get_fullname(); ?> avatar" />
            </a>

            <div class="ps-post__meta">
                <div class="ps-post__title">
                    <?php $PeepSoActivity->post_action_title(); ?>
                    <span class="ps-post__subtitle ps-js-activity-extras"><?php
                        $post_extras = apply_filters('peepso_post_extras', array());
                        if(is_array($post_extras)) {
                            echo implode(' ', $post_extras);
                        }
                        ?></span>
                </div>
                <div class="ps-post__info">

                    <?php $PeepSoActivity->post_edit_notice(); ?>

                    <a class="ps-post__date ps-js-timestamp" href="<?php $PeepSoActivity->post_link(); ?>" data-timestamp="<?php $PeepSoActivity->post_timestamp(); ?>"><?php $PeepSoActivity->post_age(); ?></a>
                    <?php if (apply_filters('peepso_activity_has_privacy', TRUE)) { ?>
                        <div class="ps-post__privacy ps-dropdown ps-dropdown--privacy ps-js-dropdown ps-js-privacy--<?php echo $act_id; ?>" title="<?php echo __('Post privacy', 'peepso-core');?>">
                            <a href="#" data-value="" class="ps-post__privacy-toggle ps-dropdown__toggle ps-js-dropdown-toggle">
                                <div class="ps-post__privacy-label dropdown-value">
                                    <?php $PeepSoActivity->post_access(); ?>
                                </div>
                            </a>
                            <?php wp_nonce_field('change_post_privacy_' . $act_id, '_privacy_wpnonce_' . $act_id); ?>
                            <?php echo $PeepSoPrivacy->render_dropdown('activity.change_post_privacy(this, ' . $act_id . ')'); ?>
                        </div>
                    <?php } ?>

                </div>
            </div>

            <?php if (is_user_logged_in() && apply_filters('peepso_show_post_options', TRUE)) { ?>
                <div class="ps-post__options ps-js-post-options" data-id="<?php echo $ID ?>">
                    <div class="ps-post__options-menu ps-js-dropdown">
                        <a href="#" class="ps-dropdown__toggle ps-js-dropdown-toggle">
                            <span class="gcis gci-ellipsis-h"></span>
                        </a>
                        <div class="ps-dropdown__menu ps-js-dropdown-menu">
                            <div style="text-align:center">
                                <span class="gcis gci-spinner gci-spin"></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

        </div>


        <div class="ps-post__body ps-js-post-body">
            <div class="ps-post__content <?php echo $nsfw ? 'ps-post__content--nsfw' : ''; ?> ps-js-activity-content ps-js-activity-content--<?php echo $act_id; ?>"><?php $PeepSoActivity->content(); ?></div>
            <div class="ps-post__content ps-post__content--edit ps-js-activity-edit ps-js-activity-edit--<?php echo $act_id; ?>" style="display:none"></div>
            <div class="ps-post__attachments <?php echo $nsfw ? 'ps-post__attachments--nsfw' : ''; ?> ps-stream-attachments ps-js-activity-attachments js-stream-attachments"><?php $PeepSoActivity->post_attachment(); ?></div>
            <?php if ($nsfw) { ?>
                <div class="ps-post__nsfw ps-js-post-nsfw">
			<span class="ps-tooltip" data-tooltip="<?php echo esc_attr(__('Reveal sensitive content.', 'peepso-core')); ?>">
				<i class="gcis gci-eye"></i>
			</span>
                </div>
            <?php } ?>
        </div>
    </div>
<?php }