<?php
if(PeepSo::get_option('site_reporting_enable', TRUE)) {
$ignore_label = __('Ignore all reports','peepso-core');
$unpublished = isset($unpublished) && $unpublished;
if($unpublished) {
    $ignore_label = __('Ignore reports & publish','peepso-core');
}

$context = $context ?? FALSE;
?>
<div class="ps-post-reports ps-js-reports ps-js-reports-<?php echo $type; ?>">
    <div class="ps-post-reports__head">
        <div class="ps-post-reports__title">
            <span>
                <i class="gcis gci-circle-exclamation"></i>
                <?php echo sprintf(_n('%d report', '%d reports', $reported,'peepso-core'), $reported);?>
            </span>

            <?php if($unpublished) { echo "<i class='gcis gci-eye-slash ps-tip ps-tip--inline' aria-label='".__('Unpublished','peepso-core')."'></i>"; } ?>

            <a class="ps-post-reports__ignore" href="#" onclick="activity.ignore_reports('<?php echo $post_id ?>', '<?php echo $module_id ?>', '<?php echo $type ?>'); return false;">
                <i class="gcis gci-circle-check"></i>
                <?php echo $ignore_label;?>
            </a>

            <?php if($type!=='profile') { ?>
            <a class="ps-post-reports__delete" href="#" onclick="activity.delete_reported('<?php echo $post_id ?>', '<?php echo $module_id ?>', '<?php echo $type ?>'); return false;">
                <i class="gcis gci-trash"></i>
                <?php echo __('Delete permanently','peepso-core');?>
            </a>
            <?php } ?>

            <?php if($context) { ?>
            <a href="<?php echo $context; ?>" target="_blank" class="ps-tip" aria-label="<?php echo __('Full view','peepso-core'); ?>"><i class="gcis gci-external-link"></i></a>
            <?php } ?>

        </div>
        <span class="ps-post-reports__toggle ps-tip"
                aria-label="<?php echo esc_attr(__('Expand','peepso-core')); ?>"
                aria-label-expand="<?php echo esc_attr(__('Expand','peepso-core')); ?>"
                aria-label-collapse="<?php echo esc_attr(__('Collapse','peepso-core')); ?>"
                onclick="activity.toggle_reports(this); return false">
            <i class="gcis gci-angle-down" class-expand="gcis gci-angle-down" class-collapse="gcis gci-angle-up"></i>
        </span>
    </div>

    <div class="ps-post-reports__list ps-js-reports-list" style="display:none">
    <?php

    foreach($reports as $report) {

        $author = PeepSoUser::get_instance($report['rep_user_id']);

        $url = $author->get_profileurl();
        $avatar = $author->get_avatar();

        $reason = $report['rep_reason'];
        $desc = $report['rep_desc'];

        $date = date(PeepSo::get_option_new('date_format_no_year'), strtotime($report['rep_timestamp']));
        ?>

        <div class="ps-post-report">
            <a class="ps-avatar ps-avatar--post ps-post-report__avatar" style="--width: var(--small);" href="<?php echo $url;?>" target="_blank">
                <img src="<?php echo $avatar;?>"  />
            </a>
            <div class="ps-post-report__data">
                <div class="ps-post-report__reason">
                    <div class="ps-post-report__title">
                        <a href="<?php echo $url;?>" target="_blank"><?php echo $author->get_fullname();?></a> :
                        <?php echo $reason;?>
                    </div>
                    <div class="ps-post-report__time"><?php echo $date;?></div>
                </div>
                <div class="ps-post-report__info">
                    <?php echo $desc;?>
                </div>
            </div>
        </div>

        <?php
    }

    ?>
    </div>
</div>
<?php }