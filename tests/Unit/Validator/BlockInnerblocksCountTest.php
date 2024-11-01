<?php
/**
 * Class file for BlockInnerblocksCountTest
 *
 * (c) Alley <info@alley.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package wp-match-blocks
 */

namespace Alley\WP\Tests\Unit\Validator;

use Alley\WP\Validator\Block_InnerBlocks_Count;
use Mantle\Testkit\Test_Case;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Unit tests for the inner blocks count validator.
 */
final class BlockInnerblocksCountTest extends Test_Case {
	/**
	 * Test that valid input with the given options is marked as valid.
	 *
	 * @param mixed $input   Input.
	 * @param array $options Options.
	 */
	#[DataProvider( 'data_valid_input' )]
	public function test_valid_input( $input, $options ) {
		$validator = new Block_InnerBlocks_Count( $options );
		$this->assertTrue( $validator->isValid( $input ) );
	}

	/**
	 * Data provider for testing valid input.
	 *
	 * @return array
	 */
	public static function data_valid_input() {
		$block = self::block();

		return [
			[
				$block,
				[
					'operator' => '==',
					'count'    => 3,
				],
			],
			[
				$block,
				[
					'operator' => '===',
					'count'    => 3,
				],
			],
			[
				$block,
				[
					'operator' => '!=',
					'count'    => 42,
				],
			],
			[
				$block,
				[
					'operator' => '<>',
					'count'    => 42,
				],
			],
			[
				$block,
				[
					'operator' => '!==',
					'count'    => 42,
				],
			],
			[
				$block,
				[
					'operator' => '<',
					'count'    => 42,
				],
			],
			[
				$block,
				[
					'operator' => '>',
					'count'    => 0,
				],
			],
			[
				$block,
				[
					'operator' => '<=',
					'count'    => 3,
				],
			],
			[
				$block,
				[
					'operator' => '>=',
					'count'    => 3,
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
		$validator = new Block_InnerBlocks_Count( $options );
		$this->assertFalse( $validator->isValid( $input ) );
		$this->assertSame( $messages, $validator->getMessages() );
	}

	/**
	 * Data provider for testing invalid input.
	 *
	 * @return array
	 */
	public static function data_invalid_input() {
		$block = self::block();

		return [
			[
				$block,
				[
					'operator' => '==',
					'count'    => 0,
				],
				[
					'not_equal' => 'Number of inner blocks must be 0 but is 3.',
				],
			],
			[
				$block,
				[
					'operator' => '===',
					'count'    => 0,
				],
				[
					'not_identical' => 'Number of inner blocks must be 0 but is 3.',
				],
			],
			[
				$block,
				[
					'operator' => '!=',
					'count'    => 3,
				],
				[
					'is_equal' => 'Number of inner blocks must not be 3.',
				],
			],
			[
				$block,
				[
					'operator' => '<>',
					'count'    => 3,
				],
				[
					'is_equal' => 'Number of inner blocks must not be 3.',
				],
			],
			[
				$block,
				[
					'operator' => '!==',
					'count'    => 3,
				],
				[
					'is_identical' => 'Number of inner blocks must not be 3.',
				],
			],
			[
				$block,
				[
					'operator' => '<',
					'count'    => 0,
				],
				[
					'not_less_than' => 'Number of inner blocks must be less than 0 but is 3.',
				],
			],
			[
				$block,
				[
					'operator' => '>',
					'count'    => 42,
				],
				[
					'not_greater_than' => 'Number of inner blocks must be greater than 42 but is 3.',
				],
			],
			[
				$block,
				[
					'operator' => '<=',
					'count'    => 0,
				],
				[
					'not_less_than_or_equal_to' => 'Number of inner blocks must be less than or equal to 0 but is 3.',
				],
			],
			[
				$block,
				[
					'operator' => '>=',
					'count'    => 42,
				],
				[
					'not_greater_than_or_equal_to' => 'Number of inner blocks must be greater than or equal to 42 but is 3.',
				],
			],
		];
	}

	/**
	 * Invalid combinations of comparison options should throw an exception and not validate anything.
	 */
	public function test_invalid_options() {
		$validator = new Block_InnerBlocks_Count();

		try {
			$validator->setOptions(
				[
					'operator' => 'foo',
				],
			);
		} catch ( \Exception $exception ) {
			// Do nothing.
			unset( $exception );
		}

		$this->assertFalse( $validator->isValid( self::block() ) );

		$messages = $validator->getMessages();

		$this->assertSame(
			'Invalid comparison options for count of inner blocks.',
			current( $messages ),
		);
	}

	/**
	 * Test block.
	 *
	 * @return array
	 */
	protected static function block() {
		$blocks = parse_blocks(
			<<<HTML
<!-- wp:columns -->
<div class="wp-block-columns">
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:paragraph -->
		<p>This is a column block. It has <strong>3</strong> columns.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->

	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:paragraph -->
		<p>Paragraph 2 is in the middle.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->

	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:paragraph -->
		<p>Paragraph 3 is in the last column.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->
HTML
		);

		return $blocks[0];
	}
}
