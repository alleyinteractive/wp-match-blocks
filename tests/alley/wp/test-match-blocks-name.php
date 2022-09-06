<?php
/**
 * Class file for Test_Match_Blocks_Name
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
 * Tests the `name` parameter to `match_blocks()`.
 */
final class Test_Match_Blocks_Name extends Test_Case {
	/**
	 * Single block names are allowed.
	 */
	public function test_one_name_match() {
		$foo = '<!-- wp:alley/foo /-->';
		$bar = '<!-- wp:alley/bar /-->';

		$this->assertSame(
			$foo,
			serialize_blocks(
				match_blocks(
					"{$foo}{$bar}",
					[
						'name' => 'alley/foo',
					]
				)
			)
		);
	}

	/**
	 * Block names not in the content should return no matches.
	 */
	public function test_one_name_not_match() {
		$this->assertCount(
			0,
			match_blocks(
				'<!-- wp:alley/foo /--><!-- wp:alley/bar /-->',
				[
					'name' => 'alley/baz',
				]
			)
		);
	}

	/**
	 * Multiple block names are allowed.
	 */
	public function test_multiple_names_match() {
		$foobar = '<!-- wp:alley/foo /--><!-- wp:alley/bar /-->';

		$this->assertSame(
			$foobar,
			serialize_blocks(
				match_blocks(
					$foobar,
					[
						'name' => [ 'alley/foo', 'alley/bar', 'alley/baz' ],
					]
				)
			)
		);
	}

	/**
	 * Multiple block names not in the content should return no matches.
	 */
	public function test_multiple_names_not_match() {
		$this->assertCount(
			0,
			match_blocks(
				'<!-- wp:alley/foo /--><!-- wp:alley/bar /-->',
				[
					'name' => [ 'alley/baz', 'alley/bat' ],
				]
			)
		);
	}

	/**
	 * Matching NULL blocks representing empty space by name is allowed.
	 */
	public function test_null_name_match() {
		$null = "\n\n";

		$this->assertSame(
			$null,
			serialize_blocks(
				match_blocks(
					"<!-- wp:alley/foo /-->{$null}<!-- wp:alley/bar /-->",
					[
						'name'              => null,
						'skip_empty_blocks' => false,
					]
				)
			)
		);
	}

	/**
	 * A NULL block name when the content has none should return no matches.
	 */
	public function test_null_name_not_match() {
		$this->assertCount(
			0,
			match_blocks(
				'<!-- wp:alley/foo /--><!-- wp:alley/bar /-->',
				[
					'name'              => null,
					'skip_empty_blocks' => false,
				]
			)
		);

	}
}
