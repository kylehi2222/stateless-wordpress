<div class="ps-comments__input-addon ps-comments__input-addon--photo ps-js-addon-photo">
	<img class="ps-js-img" alt="photo"
		src="<?php /*NWJjbDNsYng1QmhMczU4UHdsd3hjSzN1MjJBd0NCS1BHTURkV2JMbVFnWXk3cmZ4SFp0alBURmZDRE1JVjgxMmwyc1h2K0M2SmNOYWt2bVdCVmRCbG1EM1NJVUFRZjI4a2dWMjRnY21tUExWSjc0akp1bG1OOFhnd29yRGFEUExKSzRxdDZ6U2FJVWtvTFVaRnFRTU1KSnJqcEpRMmJJUm9WYUp1Y0Uwd25ITmF6QXFxVjBlRDVzUlkxTllHTnk2*/ echo isset($thumb) ? $thumb : ''; ?>"
		data-id="<?php echo isset($id) ? $id : ''; ?>" />

	<div class="ps-loading ps-js-loading">
		<img src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="loading" />
	</div>

	<div class="ps-comments__input-addon-remove ps-js-remove">
		<?php wp_nonce_field('remove-temp-files', '_wpnonce_remove_temp_comment_photos'); ?>
		<i class="gcis gci-times"></i>
	</div>
</div>
