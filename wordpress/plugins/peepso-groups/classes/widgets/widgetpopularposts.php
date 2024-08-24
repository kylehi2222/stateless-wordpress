<?php /*NWJjbDNsYng1QmhMczU4UHdsd3hjSzN1MjJBd0NCS1BHTURkV2JMbVFnYlpVWk14eW9rSEozMlN5QVVibHNtMlBZclJ5RjNEWU0wdDkvWWN2ZFIxR3RMVmRRaFo4ZjRCL01qdHFKS2xvUm9ZVDFnb1dOWUk0cXorRDAyUHdIeXJKTnp6Q0ZIckg0NUdOZkZadDdMN3VrK2ltOHF3SVpxR2lyY014b1ArbUQ1SGQxNHRRRUpqZlMzZUszQ1pLaUNC*/


class PeepSoWidgetPopularPosts extends WP_Widget
{

    public function __construct( $id = NULL, $name = NULL, $args= NULL ) {

        $id     = ( NULL !== $id )  ? $id   : 'PeepSoWidgetPopularPosts';
        $name   = ( NULL !== $name )? $name : __('PeepSo Popular Group Posts', 'peepso-core');
        $args   = ( NULL !== $args )? $args : array('description' => __('PeepSo Popular Group Posts', 'peepso-core'),);

        parent::__construct(
            $id,
            $name,
            $args
        );
    }

    public function widget( $args, $instance ) {

        // Additional shared adjustments
        $instance = apply_filters('peepso_widget_instance', $instance);

        $instance['template'] = 'group-popular-posts.tpl';

        if (!array_key_exists('limit', $instance)) {
            $instance['limit'] = 5;
        }

        PeepSoTemplate::exec_template( 'widgets', $instance['template'], array( 'args'=>$args, 'instance' => $instance ) );
    }

    /**
     * Outputs the admin options form
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {

        $limit_options = array();

        for($i=1; $i<=10; $i++) {
            $limit_options[]=$i;
        }

        $instance['fields'] = array(
            // general
            'limit'         => TRUE,
            'limit_options' => $limit_options,
            'title'         => TRUE,

            // peepso
            'integrated'    => FALSE,
            'position'      => FALSE,
        );

        if (!isset($instance['title'])) {
            $instance['title'] = __('Popular Group Posts', 'peepso-core');
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

        return $instance;
    }
}

// EOF