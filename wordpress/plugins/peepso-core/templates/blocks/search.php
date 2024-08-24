<?php

// Get block settings.
[
    'title' => $title,
] = $attributes;

?><div class="ps-widget__wrapper--external ps-widget--external ps-js-widget-search">
    <div class="ps-widget__header--external">
        <?php if (isset($widget_instance['before_title'])) echo $widget_instance['before_title']; ?>
        <?php echo $title; ?>
        <?php if (isset($widget_instance['after_title'])) echo $widget_instance['after_title']; ?>
    </div>
    <div class="ps-widget__body--external">
        <div class="ps-widget--search">
            <?php PeepSoTemplate::exec_template('search', 'search', array('context' => 'widget')); ?>
        </div>
    </div>
</div>
