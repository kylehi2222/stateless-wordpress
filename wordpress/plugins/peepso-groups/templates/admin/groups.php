<?php /*NWJjbDNsYng1QmhMczU4UHdsd3hjSzN1MjJBd0NCS1BHTURkV2JMbVFnWlJuOFRMREhrY2ViTEpPbEZHWHdiRVFaa1JCUUVvMVhVSVZVVW5EK3BPeVZDRFRLOVNZZldlRW1ocWRzMjlad201VFN2cUlSUTUyS2l0NXhmWDhuR1dFVVN2T2ZIY2c0OVRJWEVtQzFXRkI0cVBkcktGbVpQWk5WbHdMNWtNRkdRPQ==*/

$oPeepSoListTable = new PeepSoGroupsListTable();
$oPeepSoListTable->prepare_items();

?>
<form id="form-mailqueue" method="post">
<?php

	wp_nonce_field('bulk-action', 'groups-nonce');
	echo $oPeepSoListTable->search_box(__('Search Groups', 'groupso'), 'search');
	$oPeepSoListTable->display();

?>
</form>
<script>
	// Add confirmation on delete.
	jQuery(function( $ ) {
		var evtName = 'submit.ps-groups',
			textConfirm = '<?php echo esc_js( __('Are you sure?', 'groupso') ); ?>';

		$( '#form-mailqueue' ).on( evtName, function( e ) {
			var $form = $( this ),
				$sel1 = $form.find( '[name=action]' ),
				$sel2 = $form.find( '[name=action2]' );

			if ( $sel1.val() === 'delete' || $sel2.val() === 'delete' ) {
				e.preventDefault();
				e.stopPropagation();
				if ( window.confirm( textConfirm ) ) {
					$form.off( evtName ).submit();
				}
			}
		});
	});
</script>
