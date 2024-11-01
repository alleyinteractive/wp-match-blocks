<?php
/**
 * Class file for MatchBlocksPositionTest
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
 * Tests the `position` parameter to `match_blocks()`.
 */
final class MatchBlocksPositionTest extends Test_Case {
	/**
	 * Example block HTML.
	 *
	 * @var string
	 */
	private const P1 = '<!-- wp:paragraph --><p>First</p><!-- /wp:paragraph -->';

	/**
	 * Example block HTML.
	 *
	 * @var string
	 */
	private const P2 = '<!-- wp:paragraph --><p>Second</p><!-- /wp:paragraph -->';

	/**
	 * Example block HTML.
	 *
	 * @var string
	 */
	private const P3 = '<!-- wp:paragraph --><p>Third</p><!-- /wp:paragraph -->';

	/**
	 * Example block HTML.
	 *
	 * @var string
	 */
	private const BLOCKS = self::P1 . self::P2 . self::P3;

	/**
	 * Positive positions are allowed.
	 */
	public function test_positive_position_match() {
		$this->assertSame(
			self::P2,
			serialize_blocks(
				match_blocks(
					self::BLOCKS,
					[ 'position' => 1 ]
				)
			)
		);
	}

	/**
	 * Out-of-bounds positive positions return no matches.
	 */
	public function test_positive_position_not_match() {
		$this->assertCount(
			0,
			match_blocks(
				self::BLOCKS,
				[ 'position' => \count( parse_blocks( self::BLOCKS ) ) + 1 ]
			)
		);
	}

	/**
	 * Negative positions are allowed.
	 */
	public function test_negative_position_match() {
		$this->assertSame(
			self::P1,
			serialize_blocks(
				match_blocks(
					self::BLOCKS,
					[ 'position' => -3 ]
				)
			)
		);
	}

	/**
	 * Out-of-bounds negative positions return no matches.
	 */
	public function test_negative_position_not_match() {
		$this->assertCount(
			0,
			match_blocks(
				self::BLOCKS,
				[ 'position' => -1 - \count( parse_blocks( self::BLOCKS ) ) ]
			)
		);
	}

	/**
	 * Multiple positive and negative positions can be combined.
	 */
	public function test_multiple_positions_match() {
		$this->assertSame(
			self::P1 . self::P3,
			serialize_blocks(
				match_blocks(
					self::BLOCKS,
					[ 'position' => [ 0, -1 ] ]
				)
			)
		);
	}

	/**
	 * Multiple out-of-bounds positions return no matches.
	 */
	public function test_multiple_positions_not_match() {
		$count = \count( parse_blocks( self::BLOCKS ) );

		$this->assertCount(
			0,
			match_blocks(
				self::BLOCKS,
				[ 'position' => [ -1 - $count, $count + 1 ] ]
			)
		);
	}
}
