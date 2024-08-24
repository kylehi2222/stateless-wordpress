<?php /*NWJjbDNsYng1QmhMczU4UHdsd3hjSzN1MjJBd0NCS1BHTURkV2JMbVFnYUpCeHJWMURIY3NLREJTalJiSTE4Z1BrQmkySC9NM083NVpoTytKNkpnZW1IVWc5M28vY1VjMXQwZGZ0Q3NZUkdvdTllMk1yZXNNT0dFc200TkhOWnZjSnJ6bDlCclh1OW04L2RhN3dVcVlmZlFUV3JmME90aDQ3V0grWUpObFdRWi8rNHhzVjdjbkNsVmZJZnRvVFR2*/
    echo $args['before_widget'];
?>

<div class="ps-widget__wrapper<?php echo $instance['class_suffix'];?> ps-widget<?php echo $instance['class_suffix'];?>">
    <div class="ps-widget__header<?php echo $instance['class_suffix'];?>">
        <?php
            if ( ! empty( $instance['title'] ) ) {
                echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
            }
        ?>
    </div>
    <div class="ps-widget__body<?php echo $instance['class_suffix'];?>">
        <div class="psw-photos">
        <?php
            if(count($instance['list']))
            {
        ?>
            <?php
                foreach ($instance['list'] as $photo)
                {
                    PeepSoTemplate::exec_template('photos', 'photo-item-widget', (array)$photo);
                }
            ?>
            <?php
                }
                else
                {
                    echo "<div class='psw-photos__info'>".__('No photos', 'picso')."</div>";
                }
            ?>
        </div>
    </div>
</div>

<?php

echo $args['after_widget'];

// EOF
