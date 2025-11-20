<?php
/**
 * Class file for MatchBlocksExperimentalXPathTest
 *
 * (c) Alley <info@alley.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package wp-match-blocks
 */

namespace Alley\WP\Tests\Unit;

use Mantle\Testkit\Test_Case;

use function Alley\WP\match_blocks;

/**
 * Tests the `__experimental_xpath` parameter to `match_blocks()`.
 */
final class MatchBlocksExperimentalXPathTest extends Test_Case {
	/**
	 * Example block HTML.
	 *
	 * @var string
	 */
	private const BLOCKS = <<<HTML
<!-- wp:paragraph -->
<p>The Common category includes the following blocks: <em>Paragraph, image, headings, list, gallery, quote, audio, cover, video.</em></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"right"} -->
<p class="has-text-align-right"><em>This italic paragraph is right aligned.</em></p>
<!-- /wp:paragraph -->

<!-- wp:image {"id":968,"sizeSlug":"full","className":"is-style-circle-mask"} -->
<figure class="wp-block-image size-full is-style-circle-mask"><img src="https://example.com/wp-content/uploads/2013/03/image-alignment-150x150-13.jpg" alt="Image Alignment 150x150" class="wp-image-968"/></figure>
<!-- /wp:image -->

<!-- wp:cover {"url":"https://example.com/wp-content/uploads/2008/06/dsc04563-12.jpg","id":759,"minHeight":274} -->
<div class="wp-block-cover has-background-dim" style="background-image:url(https://example.com/wp-content/uploads/2008/06/dsc04563-12.jpg);min-height:274px">
  <div class="wp-block-cover__inner-container">
    <!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
    <p class="has-text-align-center has-large-font-size">Cover block with background image</p>
    <!-- /wp:paragraph -->
  </div>
</div>
<!-- /wp:cover -->
HTML;

	/**
	 * Blocks matching the given XPath query should be matched.
	 */
	public function test_xpath_query() {
		$matched = match_blocks(
			self::BLOCKS,
			[
				'__experimental_xpath' => '//block[blockName="core/paragraph"]',
			],
		);

		$this->assertCount( 3, $matched );
	}

	/**
	 * Blocks matching the given XPath query should be matched.
	 */
	public function test_xpath_root_query() {
		$matched = match_blocks(
			self::BLOCKS,
			[
				'__experimental_xpath' => '/blocks/block[blockName="core/paragraph"]',
			],
		);

		$this->assertCount( 2, $matched );
	}

	/**
	 * Blocks matching the given XPath query should be matched.
	 */
	public function test_xpath_innerblocks_query() {
		$matched = match_blocks(
			self::BLOCKS,
			[
				'__experimental_xpath' => '//block[blockName="core/cover"]/innerBlocks/block[blockName="core/paragraph"]',
			],
		);

		$this->assertCount( 1, $matched );
		$this->assertSame(
			'<p class="has-text-align-center has-large-font-size">Cover block with background image</p>',
			trim( $matched[0]['innerHTML'] ),
		);
	}
}
