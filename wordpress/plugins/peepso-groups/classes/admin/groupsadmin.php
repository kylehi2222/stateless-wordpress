<?php /*NWJjbDNsYng1QmhMczU4UHdsd3hjSzN1MjJBd0NCS1BHTURkV2JMbVFnWWFKV0hyME1ScFY2TllzUXEyYUxOaytDL29lVUk3RjZGOTllNzNRODFsby9YYTZyZWoyUEI2K3lsZkIxTnlQQXNWZUk1ZmJ2UnhBRi83OUhCK08yVHBiRW92NEV5bzV6ZlRIZzJTeXVMSWw4UmNYMUZHYk8vMnhQcXF5OGoraDZvPQ==*/
/*
 * Performs tasks for Admin page requests
 * @package GroupSo
 * @author PeepSo
 */

class PeepSoGroupsAdmin
{
	private function __construct()
	{

	}

	public static function admin_page()
	{
		PeepSoTemplate::exec_template('admin', 'groups', array() );
	}

	public static function admin_header($title)
	{
		echo '<h2><img src="', PeepSo::get_asset('images/admin/logo.png'), '" width="150" />';
		echo ' v' . PeepSoGroupsPlugin::PLUGIN_VERSION;

		if(strlen(PeepSoGroupsPlugin::PLUGIN_RELEASE)) {
			echo "-" . PeepSoGroupsPlugin::PLUGIN_RELEASE;
		}

		echo ' - ' ,  $title , '</h2>', PHP_EOL;
	}
}