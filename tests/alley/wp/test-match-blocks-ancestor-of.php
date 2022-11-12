<?php
/**
 * Class file for Test_Match_Blocks_Ancestor_Of
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
 * Tests the `ancestor_of` parameter to `match_blocks()`.
 */
final class Test_Match_Blocks_Ancestor_Of extends Test_Case {
	/**
	 * Example block HTML.
	 *
	 * @var string
	 */
	private const BLOCKS = '<!-- wp:foo --><!-- wp:bar --><!-- wp:baz /--><!-- /wp:bar --><!-- /wp:foo -->';

	/**
	 * Match blocks that are ancestors of child blocks.
	 */
	public function test_of_child() {
		$actual = match_blocks(
			self::BLOCKS,
			[
				'ancestor_of' => [
					'name' => 'core/bar',
				],
			],
		);

		$this->assertCount( 1, $actual );
		$this->assertSame( 'core/foo', $actual[0]['blockName'] );
	}

	/**
	 * Match blocks that are ancestors of grandchild blocks.
	 */
	public function test_of_grandchild() {
		$actual = match_blocks(
			self::BLOCKS,
			[
				'ancestor_of' => [
					'name' => 'core/baz',
				],
			],
		);

		$this->assertCount( 1, $actual );
		$this->assertSame( 'core/foo', $actual[0]['blockName'] );
	}

	/**
	 * Clauses can be nested, i.e. match blocks that are the ancestors of blocks that are also ancestors.
	 */
	public function test_nested() {
		$actual = match_blocks(
			self::BLOCKS,
			[
				'ancestor_of' => [
					'ancestor_of' => [
						'name' => 'core/baz',
					],
				],
			],
		);

		$this->assertCount( 1, $actual );
		$this->assertSame( 'core/foo', $actual[0]['blockName'] );
	}

	/**
	 * Match blocks that are ancestors of blocks by name.
	 */
	public function test_string_param() {
		$actual = match_blocks(
			self::BLOCKS,
			[
				'ancestor_of' => 'core/bar',
			],
		);

		$this->assertCount( 1, $actual );
		$this->assertSame( 'core/foo', $actual[0]['blockName'] );
	}

	/**
	 * When combined with `flatten`, all ancestors of deeply nested blocks should match.
	 */
	public function test_with_flatten() {
		$actual = match_blocks(
			self::BLOCKS,
			[
				'ancestor_of' => 'core/baz',
				'flatten'     => true,
			],
		);

		$this->assertCount( 2, $actual );
		$this->assertSame( 'core/foo', $actual[0]['blockName'] );
		$this->assertSame( 'core/bar', $actual[1]['blockName'] );
	}

	/**
	 * No matching blocks.
	 */
	public function test_no_matches() {
		$actual = match_blocks(
			self::BLOCKS,
			[
				'ancestor_of' => [
					'name' => 'core/foo',
				],
			],
		);

		$this->assertCount( 0, $actual );
	}
}
