<?php
/**
 * Class file for Test_Match_Blocks_Skip_Empty_Blocks
 *
 * (c) Alley <info@alley.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package wp-match-blocks
 */

namespace Alley\WP;

use Mantle\Testkit\Test_Case;

/**
 * Tests the `skip_empty_blocks` parameter to `match_blocks()`.
 */
final class Test_Match_Blocks_Skip_Empty_Blocks extends Test_Case {
	/**
	 * Example block HTML.
	 *
	 * @var string
	 */
	private const BLOCKS = "<!-- wp:foo /-->\n\n<!-- wp:bar /-->";

	/**
	 * Matching only non-empty blocks is allowed.
	 */
	public function test_skip_empty_blocks_count() {
		$this->assertCount(
			2,
			match_blocks(
				self::BLOCKS,
				[
					'skip_empty_blocks' => true,
				]
			)
		);
	}

	/**
	 * Matching empty blocks is allowed.
	 */
	public function test_count_empty_blocks_count() {
		$this->assertCount(
			3,
			match_blocks(
				self::BLOCKS,
				[
					'skip_empty_blocks' => false,
				]
			)
		);
	}
}
