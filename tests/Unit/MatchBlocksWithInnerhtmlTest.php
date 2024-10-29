<?php
/**
 * Class file for MatchBlocksWithInnerhtmlTest
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
 * Tests the `with_innerhtml` parameter to `match_blocks()`.
 */
final class MatchBlocksWithInnerhtmlTest extends Test_Case {
	/**
	 * Example block HTML.
	 *
	 * @var string
	 */
	private const BLOCK = '<!-- wp:paragraph --><p>Lorem ipsum dolor</p><!-- wp:paragraph /-->';

	/**
	 * Match exact string in inner HTML.
	 */
	public function test_exact_with_innerhtml_match() {
		$this->assertNotEmpty(
			match_blocks(
				self::BLOCK,
				[
					'with_innerhtml' => ' ipsum ',
				]
			)
		);
	}

	/**
	 * Match strings in inner HTML case-insensitively.
	 */
	public function test_case_insensitive_with_innerhtml_match() {
		$this->assertNotEmpty(
			match_blocks(
				self::BLOCK,
				[
					'with_innerhtml' => 'lorem',
				]
			)
		);
	}

	/**
	 * Strings not in the HTML should return no matches.
	 */
	public function test_with_innerhtml_not_match() {
		$this->assertEmpty(
			match_blocks(
				self::BLOCK,
				[
					'with_innerhtml' => 'sit amet',
				]
			),
		);
	}
}
