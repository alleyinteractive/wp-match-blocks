<?php
/**
 * Class file for MatchBlocksHasInnerblocksTest
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
 * Tests the `has_innerblocks` parameter to `match_blocks()`.
 */
final class MatchBlocksHasInnerblocksTest extends Test_Case {
	/**
	 * Example block HTML.
	 *
	 * @var string
	 */
	private const BLOCKS = '<!-- wp:foo --><!-- wp:bar --><!-- wp:baz /--><!-- /wp:bar --><!-- /wp:foo -->';

	/**
	 * Blocks with inner blocks should be matched.
	 */
	public function test_has_innerblocks() {
		$this->assertCount(
			1,
			match_blocks(
				self::BLOCKS,
				[
					'has_innerblocks' => true,
				]
			)
		);
	}

	/**
	 * Inner blocks with their own inner blocks should be matched.
	 */
	public function test_has_flattened_innerblocks() {
		$this->assertCount(
			2,
			match_blocks(
				self::BLOCKS,
				[
					'flatten'         => true,
					'has_innerblocks' => true,
				]
			)
		);
	}

	/**
	 * Only the block without inner blocks should be matched.
	 */
	public function test_has_no_innerblocks() {
		$actual = match_blocks(
			self::BLOCKS,
			[
				'flatten'         => true,
				'has_innerblocks' => false,
			]
		);

		$this->assertCount( 1, $actual );
		$this->assertSame( 'core/baz', $actual[0]['blockName'] );
	}
}
