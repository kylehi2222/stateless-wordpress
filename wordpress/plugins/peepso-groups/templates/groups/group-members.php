<?php /*NWJjbDNsYng1QmhMczU4UHdsd3hjSzN1MjJBd0NCS1BHTURkV2JMbVFnWXpNNjhXa1Avbk1Oc0ZDanpPeC9oSHhKUlIyK1VUK2huc1ZYRXRlcTNNdXNJZCtCcVNjMnJBMmxiQUtJQUxwaTVPS2xwSm9Qc1JXRVMyUkJtTVNJK3RlNmNvRmpnRlYrSi80QmpER29KQVhsbzB5K0NMdEV3S1R4dDhlMVo4WURZOVR4OWRiRGdrTU85cHVHWm9kWjVX*/ $PeepSoGroupUser = new PeepSoGroupUser($group->id); ?>
<div class="peepso">
	<div class="ps-page ps-page--group-members">
		<?php PeepSoTemplate::exec_template('general','navbar'); ?>

		<?php if($PeepSoGroupUser->can('access')) { ?>
		<div class="ps-groups">
			<?php PeepSoTemplate::exec_template('groups', 'group-header', array('group'=>$group, 'group_segment'=>$group_segment)); ?>

			<?php if (! get_current_user_id()) { PeepSoTemplate::exec_template('general','login-profile-tab'); } ?>

			<?php
				$PeepSoGroupUser = new PeepSoGroupUser($group->id, get_current_user_id());
				PeepSoTemplate::exec_template('groups', 'group-members-tabs', array('tab' => FALSE, 'PeepSoGroupUser' => $PeepSoGroupUser, 'group' => $group));
				PeepSoTemplate::exec_template('groups', 'group-members-search-form', array());
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
