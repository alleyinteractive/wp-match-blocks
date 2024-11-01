<?php
/**
 * Class file for MatchBlocksNthOfTypeTest
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

use PHPUnit\Framework\Attributes\DataProvider;

use function Alley\WP\match_blocks;

/**
 * Tests the `nth_of_type` parameter to `match_blocks()`.
 */
final class MatchBlocksNthOfTypeTest extends Test_Case {
	/**
	 * Example block HTML.
	 *
	 * @var string
	 */
	private const P1 = '<!-- wp:paragraph --><p>First</p><!-- /wp:paragraph -->';

	/**
	 * Example block HTML.
	 *
	 * @var string
	 */
	private const P2 = '<!-- wp:paragraph --><p>Second</p><!-- /wp:paragraph -->';

	/**
	 * Example block HTML.
	 *
	 * @var string
	 */
	private const P3 = '<!-- wp:paragraph --><p>Third</p><!-- /wp:paragraph -->';

	/**
	 * Example block HTML.
	 *
	 * @var string
	 */
	private const HED = '<!-- wp:heading {"level":2} --><h2>A Picture is Worth a Thousand Words</h2><!-- /wp:heading -->';

	/**
	 * Integer `nth_of_type`s are allowed.
	 */
	public function test_one_nth_of_type_match() {
		$this->assertSame(
			self::P2,
			serialize_blocks(
				match_blocks(
					self::P1 . self::HED . self::P2 . self::P3,
					[
						'name'        => 'core/paragraph',
						'nth_of_type' => 2,
					]
				)
			)
		);
	}

	/**
	 * Out-of-bounds `nth_of_type`s should return no matches.
	 */
	public function test_one_nth_of_type_not_match() {
		$blocks = [
			self::P1,
			self::HED,
			self::P2,
			self::P3,
		];

		$this->assertEmpty(
			match_blocks(
				implode( '', $blocks ),
				[
					'name'        => 'core/paragraph',
					'nth_of_type' => \count( $blocks ) + 1,
				]
			)
		);
	}

	/**
	 * Multiple integer `nth_of_type`s are allowed.
	 */
	public function test_multiple_nth_of_type_match() {
		$this->assertSame(
			self::P1 . self::P3,
			serialize_blocks(
				match_blocks(
					self::P1 . self::HED . self::P2 . self::P3,
					[
						'name'        => 'core/paragraph',
						'nth_of_type' => [ 1, 3 ],
					]
				)
			)
		);
	}

	/**
	 * Multiple out-of=bounds `nth_of_type`s should return no matches.
	 */
	public function test_multiple_nth_of_type_not_match() {
		$blocks = [
			self::P1,
			self::HED,
			self::P2,
			self::P3,
		];
		$count  = \count( $blocks );

		$this->assertEmpty(
			match_blocks(
				implode( '', $blocks ),
				[
					'name'        => 'core/paragraph',
					'nth_of_type' => [ $count + 1, $count + 2 ],
				]
			)
		);
	}

	/**
	 * `An+B` 'nth_of_type' patterns are allowed.
	 *
	 * @param string $source        Source HTML.
	 * @param string $nth_of_type   `nth_of_type` parameter.
	 * @param string $expected_html Expected HTML.
	 */
	#[DataProvider( 'data_nth_of_type_selector' )]
	public function test_nth_of_type_selector( $source, $nth_of_type, $expected_html ) {
		$this->assertSame(
			$expected_html,
			serialize_blocks(
				match_blocks(
					$source,
					[ 'nth_of_type' => $nth_of_type ]
				)
			)
		);
	}

	/**
	 * Data provider.
	 *
	 * @return array[]
	 */
	public static function data_nth_of_type_selector() {
		$b1   = '<!-- wp:alley/b1 /-->';
		$b2   = '<!-- wp:alley/b2 /-->';
		$b3   = '<!-- wp:alley/b3 /-->';
		$b4   = '<!-- wp:alley/b4 /-->';
		$b5   = '<!-- wp:alley/b5 /-->';
		$b6   = '<!-- wp:alley/b6 /-->';
		$b7   = '<!-- wp:alley/b7 /-->';
		$b8   = '<!-- wp:alley/b8 /-->';
		$b9   = '<!-- wp:alley/b9 /-->';
		$b10  = '<!-- wp:alley/b10 /-->';
		$all  = [ $b1, $b2, $b3, $b4, $b5, $b6, $b7, $b8, $b9, $b10 ];
		$html = implode( '', $all );

		return [
			[
				$html,
				'odd',
				implode( '', [ $b1, $b3, $b5, $b7, $b9 ] ),
			],
			[
				$html,
				'even',
				implode( '', [ $b2, $b4, $b6, $b8, $b10 ] ),
			],
			[
				$html,
				'7',
				implode( '', [ $b7 ] ),
			],
			[
				$html,
				'5n',
				implode( '', [ $b5, $b10 ] ),
			],
			[
				$html,
				'n+7',
				implode( '', [ $b7, $b8, $b9, $b10 ] ),
			],
			[
				$html,
				'3n+4',
				implode( '', [ $b4, $b7, $b10 ] ),
			],
			[
				$html,
				'-n+3',
				implode( '', [ $b1, $b2, $b3 ] ),
			],
			[
				$html,
				'n',
				$html,
			],
			[
				$html,
				'0n+1',
				implode( '', [ $b1 ] ),
			],
			[
				$html,
				'4n-7',
				implode( '', [ $b1, $b5, $b9 ] ),
			],
			[
				$html,
				[
					'odd',
					'even',
					'relation' => 'OR',
				],
				$html,
			],
			[
				$html,
				[ 'n+3', '-n+8' ],
				implode( '', [ $b3, $b4, $b5, $b6, $b7, $b8 ] ),
			],
			[
				$html,
				'n+99999',
				'',
			],
			[
				$html,
				'invalid',
				'',
			],
		];
	}
}
