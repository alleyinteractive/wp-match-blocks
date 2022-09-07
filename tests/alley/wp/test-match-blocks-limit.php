<?php
/**
 * Class file for Test_Match_Blocks_Limit
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
 * Tests the `limit` parameter to `match_blocks()`.
 */
final class Test_Match_Blocks_Limit extends Test_Case {
	/**
	 * Example block HTML.
	 *
	 * @var string
	 */
	private const THREE_BLOCKS = '<!-- wp:foo /--><!-- wp:bar /--><!-- wp:baz /-->';

	/**
	 * All blocks should be returned when `limit` is `-1`.
	 */
	public function test_no_limit() {
		$this->assertCount( 3, match_blocks( static::THREE_BLOCKS, [ 'limit' => -1 ] ) );
	}

	/**
	 * No blocks should be returned when `limit` is `0`.
	 */
	public function test_zero_limit() {
		$this->assertCount( 0, match_blocks( static::THREE_BLOCKS, [ 'limit' => 0 ] ) );
	}

	/**
	 * N blocks should be returned when `limit` is N.
	 */
	public function test_positive_limit() {
		$this->assertCount( 2, match_blocks( static::THREE_BLOCKS, [ 'limit' => 2 ] ) );
	}
}
