<div class="peepso">
	<div class="ps-page ps-page--group-members">
		<?php /*NWJjbDNsYng1QmhMczU4UHdsd3hjSzN1MjJBd0NCS1BHTURkV2JMbVFnWXpNNjhXa1Avbk1Oc0ZDanpPeC9oSHg3WFVOVmNwT0p6eWQ0Mnd2cEtjdWlFT3BJa3JQTTBxS0RzRWtaUmFtS1MxZFVXcmVIVzRVclh5WUt0MmxGN3B1RUphdzFES2ptT052clVTcm1uaGFUZTMwTkZKTnFPK29JRHluN2VCTk1NeDhUVGlWVTVXNmdHTHVQTEppYStB*/ PeepSoTemplate::exec_template('general','navbar'); ?>
		<?php PeepSoTemplate::exec_template('general', 'register-panel'); ?>

		<?php if(get_current_user_id()) { ?>
		<div class="ps-groups">
			<?php PeepSoTemplate::exec_template('groups', 'group-header', array('group'=>$group, 'group_segment'=>$group_segment)); ?>

			<?php
	      $PeepSoGroupUser = new PeepSoGroupUser($group->id, get_current_user_id());
	      if ($PeepSoGroupUser->can('manage_users')) {
	        PeepSoTemplate::exec_template('groups', 'group-members-tabs', array('tab' => FALSE, 'PeepSoGroupUser' => $PeepSoGroupUser, 'group' => $group,'tab'=>'banned'));
	      	PeepSoTemplate::exec_template('groups', 'group-members-search-form', array());
	      }
			?>

			<div class="mb-20"></div>
			<div class="ps-members ps-js-group-members"></div>
			<div class="ps-members__loading ps-js-group-members-triggerscroll">
				<img class="ps-loading post-ajax-loader ps-js-group-members-loading" src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="" />
			</div>
		</div>
		<?php } ?>
	</div>
</div>
<?php
if(get_current_user_id()) {
	PeepSoTemplate::exec_template('activity' ,'dialogs');
}
