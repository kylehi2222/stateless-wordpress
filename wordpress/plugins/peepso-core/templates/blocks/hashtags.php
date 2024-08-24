<?php

// Get block settings.
[
    'title' => $title,
    'limit' => $limit,
    'displaystyle' => $displaystyle,
    'sortby' => $sortby,
    'sortorder' => $sortorder,
    'minsize' => $minsize,
] = $attributes;

// Get data.
$hashtags = $data;

$max = 0;

?><div class="ps-widget__wrapper--external ps-widget--external">
    <div class="ps-widget__header--external"><?php
        if (trim($title)) {
            echo isset($widget_instance['before_title']) ? $widget_instance['before_title'] : '<h2>';
            echo $title;
            echo isset($widget_instance['after_title']) ? $widget_instance['after_title'] : '</h2>';
        }
    ?></div>
    <?php if (count($hashtags)) { ?>
    <?php

        $result = array();
        foreach ($hashtags as $hashtag) {
            $result[$hashtag->ht_name] = $hashtag->ht_count;
            $max = max($max, $hashtag->ht_count);
        }

    ?>

    <div class="ps-widget__body--external">
        <div class="ps-widget--hashtags">
            <?php $wrapper = (1 == $displaystyle || 2 == $displaystyle) ? ' ps-widget__hashtags--list' : ''; ?>
            <div class="ps-widget__hashtags<?php echo $wrapper; ?>">
                <?php foreach ($result as $name => $count) { ?>
                <?php

                    $percentage = $max > 0 ? (round($count / $max * 10) * 10) : 100;

                    if (1 == $displaystyle) {
                        $size = '';
                    } else if (2 == $displaystyle) {
                        $size = 'ps-hashtag--size' . $percentage;
                    } else {
                        $size = 'ps-hashtag--box ps-hashtag--size' . $percentage;
                    }

                ?>
                    <a data-debug="<?php echo "#$name ($count / $percentage%)";?>" class="ps-hashtag <?php echo $size; ?>" href="<?php echo PeepSo::hashtag_url($name);?>">
                        #<?php echo $name;?>
                    </a>

                <?php } ?>
            </div>
        </div>
    </div>

    <?php } else { ?>
    <div class="ps-widget__body--external">
        <span class='ps-text--muted'><?php echo __('No hashtags', 'peepso-core');?></span>
    </div>
    <?php } ?>
</div>