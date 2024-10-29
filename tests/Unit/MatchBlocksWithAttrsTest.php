<?php
/**
 * Class file for MatchBlocksWithAttrsTest
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
 * Tests the `with_attrs` parameter to `match_blocks()`.
 */
final class MatchBlocksWithAttrsTest extends Test_Case {
	/**
	 * Example block HTML.
	 *
	 * @var string
	 */
	private const BLOCK = '<!-- wp:archives {"displayAsDropdown":true,"showPostCounts":true} /-->';

	/**
	 * Single attribute names are allowed.
	 */
	public function test_one_with_attrs_match() {
		$this->assertNotEmpty(
			match_blocks(
				self::BLOCK,
				[
					'with_attrs' => 'showPostCounts',
				]
			)
		);
	}

	/**
	 * Multiple attribute names are allowed.
	 */
	public function test_multiple_with_attrs_match() {
		$this->assertNotEmpty(
			match_blocks(
				self::BLOCK,
				[
					'with_attrs' => [ 'showPostCounts', 'displayAsDropdown' ],
				]
			)
		);
	}

	/**
	 * Attributes not in the content should return no matches.
	 */
	public function test_with_attrs_not_match() {
		$this->assertEmpty(
			match_blocks(
				self::BLOCK,
				[
					'with_attrs' => 'width',
				]
			)
		);
	}
}
