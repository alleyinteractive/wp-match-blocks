<?php
/**
 * Class file for MatchBlocksFlattenTest
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
 * Tests the `flatten` parameter to `match_blocks()`.
 */
final class MatchBlocksFlattenTest extends Test_Case {
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
