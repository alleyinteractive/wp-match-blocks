<?php
/**
 * Class file for MatchBlockTest
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

use function Alley\WP\match_block;

/**
 * Tests for the `match_block()` function.
 */
final class MatchBlockTest extends Test_Case {
	/**
	 * Matches return the parsed block.
	 */
	public function test_match() {
		$match = match_block(
			'<!-- wp:alley/foo {"bar": "bat"} /--><!-- wp:alley/foo {"fizz": "buzz"} /-->',
			[
				'name' => 'alley/foo',
			]
		);

		$this->assertNotNull( $match );
		$this->assertSame( 'bat', $match['attrs']['bar'] );
	}

	/**
	 * Non-matches return null, not empty set.
	 */
	public function test_not_match() {
		$this->assertNull(
			match_block(
				'<!-- wp:alley/foo {"bar": "bat"} /-->',
				[
					'name' => 'alley/bar',
				]
			)
		);
	}
}
