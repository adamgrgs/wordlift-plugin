<?php
/**
 * Tests: Install 3.25.0 Test.
 *
 * Test the {@link 3_25_0} class.
 *
 * @since 3.25.0
 * @package Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the Wordlift_Install_3_25_0_Test class.
 *
 * @since 3.25.0
 */
class Wordlift_Install_3_25_0_Test extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_Install_3_25_0} instance to test.
	 *
	 * @since 3.25.0
	 * @access private
	 * @var \Wordlift_Install_3_25_0 $install The {@link Wordlift_Install_3_25_0} instance.
	 */
	private $install;

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();
		$this->install = new Wordlift_Install_3_25_0();
	}

	/**
	 * Test to check whether we can create mappings table.
	 */
	public function test_can_create_mappings_table() {
		global $wpdb;
		$this->install::create_mappings_table();
		$expected_table_name = $wpdb->prefix . WL_MAPPING_TABLE_NAME;
		echo $wpdb->get_var( "SHOW TABLES LIKE '$expected_table_name'" );
	}

}
