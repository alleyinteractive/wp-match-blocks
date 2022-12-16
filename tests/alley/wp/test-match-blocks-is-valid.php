<?php
/**
 * Class file for Test_Match_Blocks_Is_Valid
 *
 * (c) Alley <info@alley.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package wp-match-blocks
 */

namespace Alley\WP;

use Alley\Validator\AlwaysValid;
use Alley\Validator\Not;
use Mantle\Testkit\Test_Case;
use WP_Block_Parser_Block;

/**
 * Tests the `is_valid` parameter to `match_blocks()`.
 */
final class Test_Match_Blocks_Is_Valid extends Test_Case {
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
