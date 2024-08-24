<div><?php /*NWJjbDNsYng1QmhMczU4UHdsd3hjSzN1MjJBd0NCS1BHTURkV2JMbVFnYVMxd3M0QStiWngvQnE3ZUd3T3c1N29PWi9Ic0poYTR2WWNicGkxd0p5djhqS2Uzbms0eGREeVhpOCs1YXdDcjJnUmcxU3dZYTdGMi9XTTZmOHVTVG13UHV1NllJVXN1UUlJTmxOV1dBMkJsYStiTytCeWRyMmNrMU1wdU50UVhlUGtsYWUvQ3lHRmdiNTl6VDNLbXBv*/ echo __('Are you sure want to remove this cover image?', 'groupso'); ?></div>

<?php

// Additional popup options (optional).
$opts = array(
	'title' => __('Remove Cover Image', 'groupso'),
	'actions' => array(
		array(
			'label' => __('Cancel', 'groupso'),
			'class' => 'ps-js-cancel'
		),
		array(
			'label' => __('Confirm', 'groupso'),
			'class' => 'ps-js-submit',
			'loading' => true,
			'primary' => true
		)
	)
);

?>
<script type="text/template" data-name="opts"><?php echo json_encode($opts); ?></script>
