<div><?php /*NWJjbDNsYng1QmhMczU4UHdsd3hjSzN1MjJBd0NCS1BHTURkV2JMbVFnYUVhNFNNWERRaC9XUmQ0MFdDRndIQ0grWkxSQ0ptTDl0eHZuV2VocHR2cnFwNjN1d0hKZERJN1U2WW5XejhYb0xSZnhhUDBkVzZRY24xM2x1ZVFFR2FRR004KzFoOUdYcmdiZmhleWx0eGhpMDRsYmFKa1RtSHpiVnd4STBodUYvQ0pHU0FZNFlzVGJxYzZRMGp5REdC*/ echo __('Are you sure want to remove this cover image?', 'groupso'); ?></div>

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
