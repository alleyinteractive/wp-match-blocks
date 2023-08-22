<?php
/**
 * Class file for Test_Nonempty_Block
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
 * Unit tests for the nonempty block validator.
 */
final class Test_Nonempty_Block extends Test_Case {
	/**
	 * Test that valid input is marked as valid.
	 */
	public function test_valid_input() {
		$blocks    = parse_blocks( '<!-- wp:paragraph --><p>Hello, world!</p><!-- /wp:paragraph -->' );
		$validator = new Nonempty_Block();

		$this->assertTrue( $validator->isValid( $blocks[0] ) );
	}

	/**
	 * Test that "Classic" blocks are not considered empty.
	 */
	public function test_classic_block() {
		$blocks    = parse_blocks( '<p>Hello, world!</p>' );
		$validator = new Nonempty_Block();

		$this->assertTrue( $validator->isValid( $blocks[0] ) );
	}

	/**
	 * Test that invalid input is marked as invalid.
	 */
	public function test_invalid_input() {
		$blocks    = parse_blocks( "\n\n" );
		$validator = new Nonempty_Block();

		$this->assertFalse( $validator->isValid( $blocks[0] ) );
		$this->assertSame(
			[
				'empty_block' => 'Block is empty.',
			],
			$validator->getMessages(),
		);
	}
}
