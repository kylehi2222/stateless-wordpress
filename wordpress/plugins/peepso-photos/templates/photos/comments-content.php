<?php /*NWJjbDNsYng1QmhMczU4UHdsd3hjSzN1MjJBd0NCS1BHTURkV2JMbVFnYkxPRFZrK2JoeXZhbkpacHE2WG1SNlZQNlh4TndrRGdmYXZqb2tYYldHSEpxQTBhTndQM3ZEeU41eExsbGhTa1QzbkdOWHJQYWdXWWVyUWhESHVRbXg3ZEdEWlpJUVh4N00xbGZMR2QvV01QaHNSL215YURDMkRsNldDRlFvblU3RXNDUFRZbXB1SVkwY2FpMDN6QXpQ*/
$PeepSoPhotos = PeepSoPhotos::get_instance();
?>
<div class="ps-media__attachment ps-media__attachment--photos cstream-attachment ps-media-photos photo-container photo-container-placeholder ps-clearfix ps-js-photos">
	<?php $PeepSoPhotos->show_photo_comments($photo); ?>
	<div class="ps-loading ps-media-loading ps-js-loading">
		<div class="ps-spinner">
			<div class="ps-spinner-bounce1"></div>
			<div class="ps-spinner-bounce2"></div>
			<div class="ps-spinner-bounce3"></div>
		</div>
	</div>
</div>
