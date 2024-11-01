<?php
/**
 * Class file for BlockNameTest
 *
 * (c) Alley <info@alley.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package wp-match-blocks
 */

namespace Alley\WP\Tests\Unit\Validator;

use Alley\WP\Validator\Block_Name;
use Mantle\Testkit\Test_Case;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Unit tests for the block name validator.
 */
final class BlockNameTest extends Test_Case {
	/**
	 * Test that valid input with the given options is marked as valid.
	 *
	 * @param mixed $input   Input.
	 * @param array $options Options.
	 */
	#[DataProvider( 'data_valid_input' )]
	public function test_valid_input( $input, $options ) {
		$validator = new Block_Name( $options );
		$this->assertTrue( $validator->isValid( $input ) );
	}

	/**
	 * Data provider for testing valid input.
	 *
	 * @return array
	 */
	public static function data_valid_input() {
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
	 * @param mixed $input    Input.
	 * @param array $options  Options.
	 * @param array $messages Expected error messages.
	 */
	#[DataProvider( 'data_invalid_input' )]
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
	public static function data_invalid_input() {
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
