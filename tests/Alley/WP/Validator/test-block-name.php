<?php
/**
 * Class file for Test_Block_Name
 *
 * (c) Alley <info@alley.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package wp-match-blocks
 */

namespace Alley\WP\Validator;

use Mantle\Testkit\Test_Case;

/**
 * Unit tests for the block name validator.
 */
final class Test_Block_Name extends Test_Case {
	/**
	 * Test that valid input with the given options is marked as valid.
	 *
	 * @dataProvider data_valid_input
	 *
	 * @param mixed $input   Input.
	 * @param array $options Options.
	 */
	public function test_valid_input( $input, $options ) {
		$validator = new Block_Name( $options );
		$this->assertTrue( $validator->isValid( $input ) );
	}

	/**
	 * Data provider for testing valid input.
	 *
	 * @return array
	 */
	public function data_valid_input() {
		$blocks = parse_blocks( '<!-- wp:paragraph --><p>Hello, world!</p><!-- /wp:paragraph -->' );

		return [
			[
				$blocks[0],
				[
					'name' => 'core/paragraph',
				],
			],
			[
				$blocks[0],
				[
					'name' => [ 'core/paragraph', 'core/list' ],
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
		$validator = new Block_Name( $options );
		$this->assertFalse( $validator->isValid( $input ) );
		$this->assertSame( $messages, $validator->getMessages() );
	}

	/**
	 * Data provider for testing invalid input.
	 *
	 * @return array
	 */
	public function data_invalid_input() {
		$blocks = parse_blocks( '<!-- wp:paragraph --><p>Hello, world!</p><!-- /wp:paragraph -->' );

		return [
			[
				$blocks[0],
				[],
				[
					'not_named' => 'Block must be named ; got core/paragraph.',
				],
			],
			[
				$blocks[0],
				[
					'name' => 'core/list',
				],
				[
					'not_named' => 'Block must be named core/list; got core/paragraph.',
				],
			],
			[
				$blocks[0],
				[
					'name' => [ 'core/heading', 'core/list' ],
				],
				[
					'name_not_in' => 'Block name must be one of [core/heading, core/list]; got core/paragraph.',
				],
			],
		];
	}
}
