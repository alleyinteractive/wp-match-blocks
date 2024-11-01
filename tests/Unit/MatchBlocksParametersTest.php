<?php
/**
 * Class file for MatchBlocksParametersTest
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
 * Tests combinations of parameters to `match_blocks()`.
 */
final class MatchBlocksParametersTest extends Test_Case {
	/**
	 * The default parameters should return all non-empty blocks.
	 */
	public function test_no_parameters() {
		$blocks = [
			'<!-- wp:alley/foo /-->',
			'<!-- wp:alley/bar /-->',
			'<!-- wp:alley/baz /-->',
		];

		$this->assertSame( implode( '', $blocks ), serialize_blocks( match_blocks( implode( "\n\n", $blocks ) ) ) );
	}

	/**
	 * Blocks must match all limiting parameters.
	 */
	public function test_multiple_parameters_intersection() {
		$b1 = '<!-- wp:alley/foo {"bar":"baz","bat":"fizz"} /-->';
		$b2 = '<!-- wp:alley/foo {"bar":"baz","bat":"buzz"} /-->';
		$b3 = '<!-- wp:alley/foo {"bar":"baz","bat":"fizzbuzz"} /-->';

		$this->assertSame(
			$b3,
			serialize_blocks(
				match_blocks(
					parse_blocks( "{$b1}{$b2}{$b3}" ),
					[
						'name'       => 'alley/foo',
						'with_attrs' => 'bar',
						'position'   => 2,
					]
				)
			)
		);
	}

	/**
	 * Parameters that translate to invalid validators should return no blocks.
	 */
	public function test_invalid_validator_parameter() {
		$this->assertEmpty(
			match_blocks(
				'<!-- wp:alley/foo /--><!-- wp:alley/bar /--><!-- wp:alley/baz /-->',
				[
					'attrs' => [
						[
							'key'          => 'foo',
							'key_operator' => 'BAR',
						],
					],
				]
			)
		);
	}
}
