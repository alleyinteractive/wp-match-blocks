<?php
/**
 * Class file for BlockAttributeTest
 *
 * (c) Alley <info@alley.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package wp-match-blocks
 */

namespace Alley\WP\Tests\Unit\Validator;

use Alley\WP\Validator\Block_Attribute;
use Mantle\Testkit\Test_Case;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Unit tests for the block attribute validator.
 */
final class BlockAttributeTest extends Test_Case {
	/**
	 * Test that valid input with the given options is marked as valid.
	 *
	 * @param mixed $input   Input.
	 * @param array $options Options.
	 */
	#[DataProvider( 'data_valid_input' )]
	public function test_valid_input( $input, $options ) {
		$validator = new Block_Attribute( $options );
		$this->assertTrue( $validator->isValid( $input ) );
	}

	/**
	 * Data provider for testing valid input.
	 *
	 * @return array
	 */
	public static function data_valid_input() {
		return [
			[
				self::block(),
				[],
			],
			[
				self::block(),
				[
					'key'          => 'mediaId',
					'key_operator' => '===',
				],
			],
			[
				self::block(),
				[
					'key'          => 'mediaId',
					'key_operator' => '===',
					'value'        => 617,
					'operator'     => '===',
				],
			],
			[
				self::block(),
				[
					'key'          => [ 'mediaType' ],
					'key_operator' => 'IN',
				],
			],
			[
				self::block(),
				[
					'key'          => [ 'mediaType' ],
					'key_operator' => 'IN',
					'value'        => [ 'image', 'video' ],
					'operator'     => 'IN',
				],
			],
			[
				self::block(),
				[
					'key'          => '/^media/',
					'key_operator' => 'REGEX',
				],
			],
			[
				self::block(),
				[
					'key'          => '/^media/',
					'key_operator' => 'REGEX',
					'value'        => '',
					'operator'     => '!=',
				],
			],
			[
				self::block(),
				[
					'key'          => [ 'level' ],
					'key_operator' => 'NOT IN',
				],
			],
			[
				self::block(),
				[
					'key'          => '/media$/',
					'key_operator' => 'NOT REGEX',
				],
			],
		];
	}

	/**
	 * Test that invalid input with the given options is marked as invalid.
	 *
	 * @param mixed $input    Input.
	 * @param array $options  Options.
	 * @param array $messages Expected error messages.
	 */
	#[DataProvider( 'data_invalid_input' )]
	public function test_invalid_input( $input, $options, $messages ) {
		$validator = new Block_Attribute( $options );
		$this->assertFalse( $validator->isValid( $input ) );
		$this->assertSame( $messages, $validator->getMessages() );
	}

	/**
	 * Data provider for testing invalid input.
	 *
	 * @return array
	 */
	public static function data_invalid_input() {
		return [
			[
				self::block(),
				[
					'key'          => 'data1',
					'key_operator' => '===',
				],
				[
					'no_matching_key' => 'Block must have attribute with eligible key.',
				],
			],
			[
				self::block(),
				[
					'key'          => 'mediaId',
					'key_operator' => '===',
					'value'        => 617,
					'operator'     => '!==',
				],
				[
					'no_matching_value' => 'Block must have attribute with eligible value.',
				],
			],
			[
				self::block(),
				[
					'key'          => [ 'data1' ],
					'key_operator' => 'IN',
				],
				[
					'no_matching_key' => 'Block must have attribute with eligible key.',
				],
			],
			[
				self::block(),
				[
					'key'          => [ 'mediaType' ],
					'key_operator' => 'IN',
					'value'        => [ 'video' ],
					'operator'     => 'IN',
				],
				[
					'no_matching_value' => 'Block must have attribute with eligible value.',
				],
			],
			[
				self::block(),
				[
					'key'          => '/^media$/',
					'key_operator' => 'REGEX',
				],
				[
					'no_matching_key' => 'Block must have attribute with eligible key.',
				],
			],
			[
				self::block(),
				[
					'key'          => '/^class/',
					'key_operator' => 'REGEX',
					'value'        => '',
					'operator'     => '==',
				],
				[
					'no_matching_value' => 'Block must have attribute with eligible value.',
				],
			],
			[
				self::block(),
				[
					'key'          => [ 'mediaId', 'mediaType', 'isStackedOnMobile', 'className' ],
					'key_operator' => 'NOT IN',
				],
				[
					'no_matching_key' => 'Block must have attribute with eligible key.',
				],
			],
			[
				self::block(),
				[
					'key'          => '/./',
					'key_operator' => 'NOT REGEX',
				],
				[
					'no_matching_key' => 'Block must have attribute with eligible key.',
				],
			],
			'block must have attributes to test' => [
				parse_blocks( '<!-- wp:alley/foo /-->' )[0],
				[
					'key' => 'bar',
				],
				[
					'no_matching_key' => 'Block must have attribute with eligible key.',
				],
			],
		];
	}

	/**
	 * Test block.
	 *
	 * @return array
	 */
	protected static function block() {
		$blocks = parse_blocks(
			<<<HTML
<!-- wp:media-text {"mediaId":617,"mediaType":"image","isStackedOnMobile":false,"className":"alignwide"} -->
HTML
		);

		return $blocks[0];
	}
}
