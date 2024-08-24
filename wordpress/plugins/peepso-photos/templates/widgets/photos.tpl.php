<?php /*NWJjbDNsYng1QmhMczU4UHdsd3hjSzN1MjJBd0NCS1BHTURkV2JMbVFnWjNkRjB4eXFTcmhtVW1XRDBCMXR6NXk5aG9RU2RVK3hpNVRBR0dCMTJtK3ZFUW5Zb2doMmg1R1VkS3hwdWluaTdPVlo5MG1NVHg4bStCaHBJMDZXRFIzbkkvT29KUmpadmJIeHBDaEVHNjdtTERNUUpUMjUxTGVPMEpURUYvVTJNPQ==*/
    echo $args['before_widget'];
    $owner = PeepSoUser::get_instance($instance['user_id']);
?>

<div class="ps-widget__wrapper<?php echo $instance['class_suffix'];?> ps-widget<?php echo $instance['class_suffix'];?>">
    <div class="ps-widget__header<?php echo $instance['class_suffix'];?>">
        <a href="<?php echo $owner->get_profileurl();?>photos"><?php
                if ( ! empty( $instance['title'] ) ) {
                    echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
                }
        ?></a>
    </div>
    <?php
    if(count($instance['list']))
    {
    ?>
    <div class="ps-widget__body<?php echo $instance['class_suffix'];?>">
        <div class="psw-photos">
          <?php
              foreach ($instance['list'] as $photo)
              {
                  PeepSoTemplate::exec_template('photos', 'photo-item-widget', (array)$photo);
              }
          ?>
          <?php
              // @TODO add template tag for "total"
          ?>
          <div class="psw-photos__more">
            <a href="<?php echo $owner->get_profileurl();?>photos">
              <?php echo __('View All', 'picso');?>
              <span>(<?php echo $instance['total'];?>)</span>
            </a>
          </div>
        </div>
    </div>
    <?php } else { ?>
    <div class="ps-widget__body<?php echo $instance['class_suffix'];?>">
      <div class="psw-photos">
        <div class="psw-photos__info"><?php echo __('No photos', 'picso');?></div>
      </div>
    </div>
    <?php } ?>
</div>

<?php

echo $args['after_widget'];

// EOF
