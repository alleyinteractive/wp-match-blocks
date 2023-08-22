<?php
/**
 * Class file for Test_Match_Blocks
 *
 * (c) Alley <info@alley.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package wp-match-blocks
 */

namespace Alley\WP;

use Mantle\Testkit\Test_Case;

/**
 * Tests the `count` parameter to `match_blocks()`.
 */
final class Test_Match_Blocks_Count extends Test_Case {
	/**
	 * `count` should be non-zero when there are blocks.
	 */
	public function test_nonzero_count() {
		$blocks = [
			'<!-- wp:foo /-->',
			'<!-- wp:bar /-->',
		];

		$this->assertSame(
			\count( $blocks ),
			match_blocks(
				implode( '', $blocks ),
				[
					'count' => true,
				],
			)
		);
	}

	/**
	 * `count` should be non-zero when there are classic blocks.
	 */
	public function test_classic_blocks_count() {
		$blocks = [
			'<!-- wp:foo /-->',
			'bar',
			'<!-- wp:baz /-->',
		];

		$this->assertSame(
			\count( $blocks ),
			match_blocks(
				implode( '', $blocks ),
				[
					'count' => true,
				],
			)
		);
	}

	/**
	 * `count` should be zero when there aren't blocks.
	 */
	public function test_zero_count() {
		$this->assertSame(
			0,
			match_blocks(
				"\n\n",
				[
					'count' => true,
				],
			)
		);
	}
}
