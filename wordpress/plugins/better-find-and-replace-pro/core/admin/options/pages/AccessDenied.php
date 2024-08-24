<?php namespace RealTimeAutoFindReplacePro\admin\options\pages;

/**
 * Class: Add New Coin
 *
 * @package Admin
 * @since 1.0.0
 * @author CodeSolz <customer-support@codesolz.net>
 */

if ( ! defined( 'CS_BFRP_VERSION' ) ) {
	die();
}

use RealTimeAutoFindReplace\lib\Util;
use RealTimeAutoFindReplace\admin\builders\FormBuilder;
use RealTimeAutoFindReplacePro\admin\builders\AdminPageBuilder;

class AccessDenied {

	/**
	 * Hold page generator class
	 *
	 * @var type
	 */
	private $Admin_Page_Generator;

	/**
	 * Form Generator
	 *
	 * @var type
	 */
	private $Form_Generator;


	public function __construct( AdminPageBuilder $AdminPageGenerator ) {
		$this->Admin_Page_Generator = $AdminPageGenerator;

		/*create obj form generator*/
		$this->Form_Generator = new FormBuilder();
	}

	/**
	 * Generate add new coin page
	 *
	 * @param type $args
	 * @return type
	 */
	public function generate_access_denided( $args ) {
		$args['body_class'] = 'no-bottom-margin';
		$args['well']       = "<ul>
                        <li class='page-permission-limited' > <b>Caution! </b> You don't have permission to access this page! Contact Administration if this is wrong!</li>
                    </ul>";

		return $this->Admin_Page_Generator->generate_page( $args );
	}

}
