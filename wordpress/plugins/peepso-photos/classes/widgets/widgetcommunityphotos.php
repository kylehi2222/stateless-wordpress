<?php /*NWJjbDNsYng1QmhMczU4UHdsd3hjSzN1MjJBd0NCS1BHTURkV2JMbVFnWWVFaTNUTS9xb1U0VlJlOVpoVk9wQ0dtckV0Qk5wWCtQR0t3TUZ5cjNEYklzbHVRaE1ZK1JKc2lBUGMrT0o4anNISGVFNUU4dXd6Y29OWEZPY0NHdDRqenVOYndPMCtyNC9IZE1TbGNPQkdPbzFSZHJoNnlSelQxYW40R09HU1VBV3lXaUdBUVlRT0l6YlJQaGdoWk5R*/


class PeepSoWidgetCommunityphotos extends WP_Widget
{

    /**
     * Set up the widget name etc
     */
    public function __construct($id = null, $name = null, $args= null) {
        if(!$id)    $id     = 'PeepSoWidgetCommunityphotos';
        if(!$name)  $name   = __('PeepSo Community Photos', 'picso');
        if(!$args)  $args   = array('description' => __( 'PeepSo Community Photos', 'picso' ), );

        parent::__construct(
           $id, // Base ID
           $name, // Name
           $args // Args
        );
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {

        if (isset($instance['is_profile_widget'])) {
            // Override the HTML wrappers
            $args = apply_filters('peepso_widget_args_internal', $args);
        }

        // Additional shared adjustments
        $instance = apply_filters('peepso_widget_instance', $instance);

        if(!array_key_exists('template', $instance) || !strlen($instance['template']))
        {
            $instance['template'] = 'community-photos.tpl';
        }

        if(!array_key_exists('limit', $instance)) {
            $instance['limit'] = 12;
        }

        if (!array_key_exists('hideempty', $instance)) {
            $instance['hideempty'] = 0;
        }

        if(!array_key_exists('search_args', $instance))
        {
            $instance['search_args'] = array(
                'number' => $instance['limit'],
            );
        }

        if(!array_key_exists('list', $instance) || !array_key_exists('total', $instance))
        {
            if (isset($_GET['legacy-widget-preview'])) {
                PeepSoSharePhotos::delete_widget_mayfly();
            }

            $state = is_user_logged_in() ? '_logged_in' : '_guest';

            $photosModel = new PeepSoPhotosModel();

            $instance['list'] = PeepSo3_Mayfly::get_or_set_if_empty('peepso_photos_widget_community' . $state, HOUR_IN_SECONDS, function() use ($photosModel, $instance) {
                return $photosModel->get_community_photos(0, $instance['limit']);
            });

            $instance['total'] = PeepSo3_Mayfly::get_or_set_if_empty('peepso_photos_widget_community_num' . $state, HOUR_IN_SECONDS, function() use ($photosModel) {
                return $photosModel->get_num_community_photos();
            });
        }

        if(0==$instance['total'] && true == $instance['hideempty']) {
            return FALSE;
        }

        wp_enqueue_script('peepso-modal-comments');

        PeepSoTemplate::exec_template( 'widgets', $instance['template'], array( 'args'=>$args, 'instance' => $instance ) );
    }

    /**
     * Outputs the admin options form
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {

        $instance['fields'] = array(
            // general
            'limit'     => TRUE,
            'title'     => TRUE,

            // peepso
            'integrated'   => TRUE,
            'position'  => TRUE,
            'ordering'  => TRUE,
            'hideempty' => TRUE,

        );

		if (!isset($instance['title'])) {
			$instance['title'] = __('Latest Photos', 'picso');
		}
		
        $this->instance = $instance;

        $settings =  apply_filters('peepso_widget_form', array('html'=>'', 'that'=>$this,'instance'=>$instance));
        echo $settings['html'];
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title']       = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['limit']       = isset($new_instance['limit']) ? (int) $new_instance['limit'] : 12;

        $instance['integrated']  = 1;
        $instance['hideempty']   = isset($new_instance['hideempty']) ? (int) $new_instance['hideempty'] : 0;
        $instance['position']    = isset($new_instance['position']) ? strip_tags($new_instance['position']) : 0;

        return $instance;
    }
}

// EOF