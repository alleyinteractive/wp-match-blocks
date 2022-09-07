<?php
/**
 * Class file for Test_Match_Blocks_Flatten
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
 * Tests the `flatten` parameter to `match_blocks()`.
 */
final class Test_Match_Blocks_Flatten extends Test_Case {
	/**
	 * Returning inner blocks via `flatten` is allowed.
	 */
	public function test_flatten_count() {
		$this->assertCount(
			3,
			match_blocks(
				'<!-- wp:foo --><!-- wp:bar --><!-- wp:baz /--><!-- /wp:bar --><!-- /wp:foo -->',
				[
					'flatten' => true,
				]
			)
		);
	}
}
