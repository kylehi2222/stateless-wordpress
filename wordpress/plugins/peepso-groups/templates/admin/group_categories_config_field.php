<?php /*NWJjbDNsYng1QmhMczU4UHdsd3hjSzN1MjJBd0NCS1BHTURkV2JMbVFnYkRMVDdRcFliV3J5amlTVjdGdDZ3dkI2UjhDeTNFV1ExY1A0bzFLVzhnWXpHMjBxNHNyUnpaLzZFZ0FjL0FYRTFnT1U0a0RVMG92Sm5qaHdKSmtmWnRxSWFoS3dGdGg5VTA2S3lCKzE0RDVYdWl4YVIveXIxL3YxREVmcXZYZEExaFp5Z1ZuUERCckpZMUNjbU1wa3doYmtRckM0OHZZNGRHQW5iVlpRY2w4dz09*/

$is_int = FALSE;
if('int' == $type) {
	$is_int = TRUE;
	$type = 'text';
}

$template_name = 'profiles_field_config_field_'.$type;
$data['data-parent-id'] = $category->id;


$container_style = array('');
if(isset($data['container_style'])) {
	$container_style[] = $data['container_style'];
	unset($data['container_style']);
}

if('checkbox' == $type && 0 == $data['admin_value']) {
	#$container_style[] = 'opacity:0.5'; // #916 do not grey it out
}

?>

<div class="ps-settings__row ps-js-fieldconf" style="<?php echo implode(';', $container_style);?>" id="<?php echo $data['id'];?>-container">
	<div class="ps-settings__label">
		<?php echo $label;
		if(isset($desc) && strlen($desc)) {
			echo " <small><a title=\"$desc\">[?]</a></small>";
		}
		?>

		<div class="ps-settings__progress ps-js-progress">
			<img src="images/loading.gif" style="display:none">
			<i class="ace-icon fa fa-check bigger-110" style="display:none"></i>
		</div>
	</div>

	<div class="ps-settings__controls">
		<?php PeepSoTemplate::exec_template('admin', $template_name, array('data'=>$data, 'label'=>$label, 'label_after'=>$label_after));?>
	</div>
</div>
