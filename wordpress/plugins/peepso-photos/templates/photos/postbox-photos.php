<div class="ps-postbox__photos">
	<div class="ps-postbox__photos-inner">
		<div class="ps-postbox__photos-fetched ps-postbox-fetched"></div>
		<div id="ps-upload-container" class="ps-postbox__photos-upload ps-postbox-input ps-inputbox">
			<div class="ps-postbox__photos-info ps-postbox-photo-upload">
				<div class="ps-postbox__photos-message"><i class="gcir gci-images"></i> <?php /*NWJjbDNsYng1QmhMczU4UHdsd3hjSzN1MjJBd0NCS1BHTURkV2JMbVFnYWMyaG9pNTdpMmdFT1FieVk0dzhQRytqRUQ4dmNRNzJqbytod0grMUhEd0JEK2gvUTNKemt5bnNMc09nRFlvbGxxZEF0OWgwK1FxMWV6SHJJOG13Z2drNExGS3FydGZVRm8zTmFwN1ZRT2tORDZhU0xtdzFZNGh4Q3ZtVHhtTndZZnlzcEtPTklSMXo3R0krRW1BR1BM*/ echo __('Click here to start uploading photos', 'picso'); ?></div>
				<?php if (isset($photo_size)) { ?>
				<div class="ps-postbox__photos-limits"><?php echo sprintf(__('Max photo dimensions: %1$s x %2$spx | Max file size: %3$sMB', 'picso'), $photo_size['max_width'], $photo_size['max_height'], $photo_size['max_size']); ?></div>
				<?php } ?>
			</div>
			<div class="ps-postbox__photos-preview ps-postbox-preview">
				<div class="ps-postbox__photos-container ps-js-photos-container"></div>
			</div>
		</div>
	</div>
</div>

<div id="photo-supported-format" style="display:none;"><?php echo __('Supported formats are: gif, jpg, jpeg, tiff, png, and webp.', 'picso'); ?></div>
<div id="photo-comment-label" style="display:none;"><?php echo __('Say something about this photo...', 'picso'); ?></div>
