<?php
/**
 * Class file for MatchedBlocksTest
 *
 * (c) Alley <info@alley.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package wp-match-blocks
 */

namespace Alley\WP\Tests\Unit\Blocks;

use Alley\WP\Blocks\Block_Content;
use Alley\WP\Blocks\Matched_Blocks;
use Mantle\Testkit\Test_Case;

/**
 * Tests the Matched_Blocks class.
 */
class MatchedBlocksTest extends Test_Case {
	/**
	 * Match blocks in the origin instance.
	 */
	public function test_serialized_blocks() {
		$matched = new Matched_Blocks(
			[
				'name' => 'alley/bar',
			],
			new Block_Content(
				<<<HTML
<!-- wp:alley/foo /-->
<!-- wp:alley/bar /-->
<!-- wp:alley/baz /-->
HTML,
			),
		);

		$this->assertSame( '<!-- wp:alley/bar /-->', $matched->serialized_blocks() );
	}
}
