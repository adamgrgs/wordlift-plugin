<?php
/**
 * Tests: Issue 662
 *
 * JSON-LD is generated by the plugin when the an AJAX call is made. JSON-LD
 * can be generated for the WebSite or for a post/entity. Issue #626 is about
 * caching the JSON-LD for posts/entities.
 *
 * The AJAX request calls the {@link Wordlift_JsonLd_Service} `get` function which
 * in turn uses `get_jsonld`. This is the function we're going to test: we want
 * to test the different caching conditions. The test context is to generated
 * some posts and entities and connect them together and see how the cache reacts
 * when the requested entity or a referenced entity is changed.
 *
 * Tests:
 * - create some posts,
 * - create some entities,
 * - connect posts to entities,
 * - connect entities to entities,
 * - get the JSON-LD of a post (1st time not cached),
 * - get the JSON-LD of a post (2nd time cached) ,
 * - get the JSON-LD of an entity (1st time not cached),
 * - change publisher, invalidate cache.
 *
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */

/**
 * Define the {@link Wordlift_Issue_626} class.
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/tests
 */
class Wordlift_Issue_626 extends Wordlift_Unit_Test_Case {

	/**
	 * The {@link Wordlift_JsonLd_Service} instance.
	 *
	 * @since 3.16.0
	 *
	 * @var \Wordlift_Jsonld_Service $jsonld_service The {@link Wordlift_JsonLd_Service} instance.
	 */
	private $jsonld_service;

	/**
	 * The {@link Wordlift_Sample_Data_Service} instance.
	 *
	 * @since 3.16.0
	 *
	 * @var \Wordlift_Sample_Data_Service $sample_data_service The {@link Wordlift_Sample_Data_Service} instance.
	 */
	private $sample_data_service;

	/**
	 * The {@link Wordlift_Cached_Post_Converter} instance.
	 *
	 * @since 3.16.0
	 * @var \Wordlift_Cached_Post_Converter $cached_postid_to_jsonld_converter The {@link Wordlift_Cached_Post_Converter} instance.
	 */
	private $cached_postid_to_jsonld_converter;

	/**
	 * @var \Wordlift_Post_To_Jsonld_Converter $post_to_jsonld_converter
	 */
	private $post_to_jsonld_converter;

	/**
	 * @inheritdoc
	 */
	function setUp() {
		parent::setUp();

		$wordlift_test = $this->get_wordlift_test();

		$this->jsonld_service                    = $wordlift_test->get_jsonld_service();
		$this->sample_data_service               = $wordlift_test->get_sample_data_service();
		$this->cached_postid_to_jsonld_converter = $wordlift_test->get_cached_postid_to_jsonld_converter();
		$this->post_to_jsonld_converter          = $wordlift_test->get_post_to_jsonld_converter();

		// Clean-up the file cache.
		$file_cache_service = $wordlift_test->get_file_cache_service();
		$file_cache_service->flush();

	}

	public function test() {

		// Create the sample data.
		$this->sample_data_service->create();

		// Get the post #5 which is the one that binds to all the entities.
		$posts = get_posts( array(
			'post_name'   => 'post_5',
			'numberposts' => 1,
		) );

		// Check that we got one post.
		$this->assertCount( 1, $posts );

		// Get the first post.
		$post = current( $posts );

		// Check that we have a valid value.
		$this->assertTrue( $post instanceof WP_Post );

		// Get the cached response.
		$cached_1 = $this->cached_postid_to_jsonld_converter->convert( $post->ID, $cached_references_1, $cache_1 );

		// Expect the first response not to be cached.
		$this->assertFalse( $cache_1 );

		// Get the original - non-cached - response.
		$original_1 = $this->post_to_jsonld_converter->convert( $post->ID );

		// Check that the responses match.
		$this->assertEquals( $original_1, $cached_1 );

		// Call again the cached converter.
		$this->cached_postid_to_jsonld_converter->convert( $post->ID, $cached_references_2, $cache_2 );

		// Check that we have a cached response this time.
		$this->assertTrue( $cache_2 );

		// Check that the responses match.
		$this->assertEquals( $original_1, $cached_1 );

		// Delete the sample data.
		$this->sample_data_service->delete();

	}


}
