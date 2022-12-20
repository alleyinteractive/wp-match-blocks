<?php
/**
 * Class file for Test_Match_Blocks_Internals
 *
 * (c) Alley <info@alley.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package wp-match-blocks
 */

namespace Alley\WP\Internals;

use Mantle\Testkit\Test_Case;

/**
 * Unit tests for internal functions for `match_blocks()`.
 */
final class Test_Match_Blocks_Internals extends Test_Case {
	/**
	 * `Internals\parse_attrs_clauses()` should throw when it can't generate validators.
	 *
	 * @throws \Exception Unused.
	 */
	public function test_empty_parse_attrs_clauses() {
		$this->expectException( \Exception::class );

		parse_attrs_clauses( [] );
	}
}
