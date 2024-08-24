<div id="peepso" class="ps-page--group-categories wrap">
	<?php /*NWJjbDNsYng1QmhMczU4UHdsd3hjSzN1MjJBd0NCS1BHTURkV2JMbVFnYkRMVDdRcFliV3J5amlTVjdGdDZ3dnlHc1JuSDF0WWpTUkdoam1EQmxSdHA1bVhPVVg4ZUd1MCtSNndXY3NJdVc3dXVWYWZMS0JxRWRDTmlYWksxZ0J0UDVxdnFSMVYzS0xRR3hkaEppby9BRVVqTmdyUXB0VjFYTDFzU1NYRnlPZW5JdlJKL3RhbUd4VWVWYldZdXF3*/ PeepSoTemplate::exec_template('admin','group_categories_button'); ;?>

	<div class="ps-js-group-categories-container ps-postbox--settings__wrapper">
		<?php

		foreach($data as $key => $category) {
			PeepSoTemplate::exec_template('admin','group_categories', array('category'=>$category));
		}

		?>
	</div>
</div>
