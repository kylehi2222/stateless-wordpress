<?php
if(PeepSo::is_admin() && $reported) {
    $PeepSoActivity = PeepSoActivity::get_instance();
    $PeepSoUser = PeepSoUser::get_instance($post_author);
    $fullName = $PeepSoUser->get_fullname();
    $unpublished = 'pending' == $post_status;

    ?>
    <div class="ps-post">
        <?php PeepSoTemplate::exec_template('activity','post-reports', ['post_id'=>$ID, 'module_id'=>$act_module_id, 'type'=>'comment', 'reported'=>$reported, 'reports'=>$reports,'context'=>$PeepSoActivity->comment_link(FALSE),'unpublished'=>$unpublished]); ?>
        <div id="comment-item-<?php echo $ID; ?>" class="ps-comment ps-comment-item cstream-comment stream-comment ps-js-comment-item <?php echo (TRUE == $unpublished) ? 'ps-comment--unpublished ps-js-comment-unpublished' : ''?>" data-comment-id="<?php echo $ID; ?>" data-author="<?php echo $post_author; ?>">
            <div class="ps-comment__avatar ps-avatar ps-avatar--comment">
                <a href="<?php echo $PeepSoUser->get_profileurl(); ?>">
                    <img data-author="<?php echo $post_author; ?>" src="<?php echo PeepSoUser::get_instance($post_author)->get_avatar(); ?>" alt="<?php echo __($fullName); ?> avatar" />
                </a>
            </div>
            <div class="ps-comment__body js-stream-content ps-js-comment-body">

                Comment by <div class="ps-comment__author">
                    @peepso_user_<?php echo $post_author; ?>(<?php echo $fullName; ?>)
                </div>

                <div class="ps-comment__content stream-comment-content ps-js-comment-content" data-type="stream-comment-content">
                    <?php $PeepSoActivity->content(); ?>
                </div>

                <div class="ps-comment__attachments ps-comment-media js-stream-attachments ps-js-comment-attachment"><?php $PeepSoActivity->comment_attachment(); ?></div>

                <div class="ps-comment__meta">
                    <div class="ps-comment__info">
                        <?php
                        $PeepSoActivity->post_edit_notice();
                        ?>
                        <span class="activity-post-age activity-post-age-text-only" data-timestamp="<?php $PeepSoActivity->post_timestamp(); ?>" style="display:none">
                        <?php $PeepSoActivity->post_age(); ?>
                    </span>
                        <span class="activity-post-age activity-post-age-link" data-timestamp="<?php $PeepSoActivity->post_timestamp(); ?>">
                        <a target="_blank" href="<?php $PeepSoActivity->comment_link(); ?>">
                        <?php $PeepSoActivity->post_age(); ?>
                        </a>
                    </span>
                    </div>
                </div>

                <div class="ps-comment__actions-dropdown ps-dropdown--left ps-js-dropdown">
                    <a href="javascript:" class="ps-dropdown__toggle ps-js-dropdown-toggle"><i class="gcis gci-ellipsis-h"></i></a>
                    <div class="ps-dropdown__menu ps-js-dropdown-menu">
                        <?php $PeepSoActivity->comment_actions_dropdown(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php }