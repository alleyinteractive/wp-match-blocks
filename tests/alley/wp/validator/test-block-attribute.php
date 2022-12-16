<?php
/**
 * Class file for Test_Block_Attribute
 *
 * (c) Alley <info@alley.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package wp-match-blocks
 */

namespace Alley\WP\Validator;

use Mantle\Testkit\Test_Case;

/**
 * Unit tests for the block attribute validator.
 */
final class Test_Block_Attribute extends Test_Case {
	/**
	 * Test that valid input with the given options is marked as valid.
	 *
	 * @dataProvider data_valid_input
	 *
	 * @param mixed $input   Input.
	 * @param array $options Options.
	 */
	public function test_valid_input( $input, $options ) {
		$validator = new Block_Attribute( $options );
		$this->assertTrue( $validator->isValid( $input ) );
	}

	/**
	 * Data provider for testing valid input.
	 *
	 * @return array
	 */
	public function data_valid_input() {
		return [
			[
				$this->block(),
				[],
			],
			[
				$this->block(),
				[
					'key'          => 'mediaId',
					'key_operator' => '===',
				],
			],
			[
				$this->block(),
				[
					'key'          => 'mediaId',
					'key_operator' => '===',
					'value'        => 617,
					'operator'     => '===',
				],
			],
			[
				$this->block(),
				[
					'key'          => [ 'mediaType' ],
					'key_operator' => 'IN',
				],
			],
			[
				$this->block(),
				[
					'key'          => [ 'mediaType' ],
					'key_operator' => 'IN',
					'value'        => [ 'image', 'video' ],
					'operator'     => 'IN',
				],
			],
			[
				$this->block(),
				[
					'key'          => '/^media/',
					'key_operator' => 'REGEX',
				],
			],
			[
				$this->block(),
				[
					'key'          => '/^media/',
					'key_operator' => 'REGEX',
					'value'        => '',
					'operator'     => '!=',
				],
			],
			[
				$this->block(),
				[
					'key'          => [ 'level' ],
					'key_operator' => 'NOT IN',
				],
			],
			[
				$this->block(),
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
	 * @dataProvider data_invalid_input
	 *
	 * @param mixed $input    Input.
	 * @param array $options  Options.
	 * @param array $messages Expected error messages.
	 */
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
	public function data_invalid_input() {
		return [
			[
				$this->block(),
				[
					'key'          => 'data1',
					'key_operator' => '===',
				],
				[
					'no_matching_key' => 'Block must have attribute with eligible key.',
				],
			],
			[
				$this->block(),
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
				$this->block(),
				[
					'key'          => [ 'data1' ],
					'key_operator' => 'IN',
				],
				[
					'no_matching_key' => 'Block must have attribute with eligible key.',
				],
			],
			[
				$this->block(),
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
				$this->block(),
				[
					'key'          => '/^media$/',
					'key_operator' => 'REGEX',
				],
				[
					'no_matching_key' => 'Block must have attribute with eligible key.',
				],
			],
			[
				$this->block(),
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
				$this->block(),
				[
					'key'          => [ 'mediaId', 'mediaType', 'isStackedOnMobile', 'className' ],
					'key_operator' => 'NOT IN',
				],
				[
					'no_matching_key' => 'Block must have attribute with eligible key.',
				],
			],
			[
				$this->block(),
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
	protected function block() {
		$blocks = parse_blocks(
			<<<HTML
<!-- wp:media-text {"mediaId":617,"mediaType":"image","isStackedOnMobile":false,"className":"alignwide"} -->
HTML
		);

		return $blocks[0];
	}
}
