<?php
/**
 * Class file for MatchBlocksIsValidTest
 *
 * (c) Alley <info@alley.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package wp-match-blocks
 */

namespace Alley\WP\Tests\Unit;

use Alley\Validator\AlwaysValid;
use Alley\Validator\Not;
use Mantle\Testkit\Test_Case;

use function Alley\WP\match_blocks;

/**
 * Tests the `is_valid` parameter to `match_blocks()`.
 */
final class MatchBlocksIsValidTest extends Test_Case {
	/**
	 * Match blocks that pass the validator.
	 */
	public function test_valid_input() {
		$blocks = parse_blocks( '<!-- wp:foo /-->' );

		$this->assertSame(
			$blocks,
			match_blocks(
				$blocks,
				[
					'is_valid' => new AlwaysValid(),
				]
			)
		);
	}

	/**
	 * Don't match blocks that don't pass the validator.
	 */
	public function test_invalid_input() {
		$this->assertCount(
			0,
			match_blocks(
				parse_blocks( '<!-- wp:foo /-->' ),
				[
					'is_valid' => new Not( new AlwaysValid(), 'lorem ipsum' ),
				]
			)
		);
	}
}
