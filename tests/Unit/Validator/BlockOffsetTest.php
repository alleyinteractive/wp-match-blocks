<?php
/**
 * Class file for BlockOffsetTest
 *
 * (c) Alley <info@alley.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package wp-match-blocks
 */

namespace Alley\WP\Tests\Unit\Validator;

use Alley\WP\Validator\Block_Offset;
use Mantle\Testkit\Test_Case;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Unit tests for the block offset validator.
 */
final class BlockOffsetTest extends Test_Case {
	/**
	 * Test that valid input with the given options is marked as valid.
	 *
	 * @param mixed $input   Input.
	 * @param array $options Options.
	 */
	#[DataProvider( 'data_valid_input' )]
	public function test_valid_input( $input, $options ) {
		$validator = new Block_Offset( $options );
		self::assertTrue( $validator->isValid( $input ) );
	}

	/**
	 * Data provider for testing valid input.
	 *
	 * @return array
	 */
	public static function data_valid_input() {
		$blocks = self::blocks();

		return [
			[
				$blocks[0],
				[
					'blocks'            => self::blocks(),
					'offset'            => 0,
					'skip_empty_blocks' => true,
				],
			],
			[
				$blocks[2],
				[
					'blocks'            => self::blocks(),
					'offset'            => 1,
					'skip_empty_blocks' => true,
				],
			],
			[
				$blocks[2],
				[
					'blocks'            => self::blocks(),
					'offset'            => 2,
					'skip_empty_blocks' => false,
				],
			],
			[
				$blocks[6],
				[
					'blocks'            => self::blocks(),
					'offset'            => -2,
					'skip_empty_blocks' => true,
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
		$validator = new Block_Offset( $options );
		self::assertFalse( $validator->isValid( $input ) );
		self::assertSame( $messages, $validator->getMessages() );
	}

	/**
	 * Data provider for testing invalid input.
	 *
	 * @return array
	 */
	public static function data_invalid_input() {
		$blocks = self::blocks();

		return [
			[
				$blocks[0],
				[],
				[
					'not_at_offset' => 'Must be at offset 0 within the blocks.',
				],
			],
			[
				$blocks[0],
				[
					'blocks'            => self::blocks(),
					'offset'            => 3,
					'skip_empty_blocks' => true,
				],
				[
					'not_at_offset' => 'Must be at offset 3 within the blocks.',
				],
			],
			[
				$blocks[0],
				[
					'blocks'            => new \ArrayIterator( $blocks ), // Test traversable.
					'offset'            => 3,
					'skip_empty_blocks' => true,
				],
				[
					'not_at_offset' => 'Must be at offset 3 within the blocks.',
				],
			],
			[
				$blocks[2],
				[
					'blocks'            => self::blocks(),
					'offset'            => 0,
					'skip_empty_blocks' => true,
				],
				[
					'not_at_offset' => 'Must be at offset 0 within the blocks.',
				],
			],
			[
				$blocks[2],
				[
					'blocks'            => self::blocks(),
					'offset'            => 99,
					'skip_empty_blocks' => false,
				],
				[
					'not_at_offset' => 'Must be at offset 99 within the blocks.',
				],
			],
			[
				$blocks[1],
				[
					'blocks'            => self::blocks(),
					'offset'            => -1,
					'skip_empty_blocks' => true,
				],
				[
					'not_at_offset' => 'Must be at offset -1 within the blocks.',
				],
			],
			[
				$blocks[0],
				[
					'blocks'            => self::blocks(),
					'offset'            => -99,
					'skip_empty_blocks' => true,
				],
				[
					'not_at_offset' => 'Must be at offset -99 within the blocks.',
				],
			],
		];
	}

	/**
	 * Data provider for testing invalid validator options.
	 *
	 * @return array
	 */
	public function data_invalid_options() {
		return [
			[
				[
					'blocks' => true,
				],
			],
		];
	}

	/**
	 * Test blocks.
	 *
	 * @return array[]
	 */
	protected static function blocks() {
		return parse_blocks(
			<<<HTML
<!-- wp:paragraph -->
<p>Headings are separate blocks as well, which helps with the outline and organization of your content.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2>A Picture is Worth a Thousand Words</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul>
	<li>Text &amp; Headings</li>
	<li>Images &amp; Videos</li>
	<li>Galleries</li>
	<li>Embeds, like YouTube, Tweets, or other WordPress posts.</li>
	<li>Layout blocks, like Buttons, Hero Images, Separators, etc.</li>
	<li>And <em>Lists</em> like this one of course :)</li>
</ul>
<!-- /wp:list -->

<!-- wp:separator -->
<hr class="wp-block-separator" />
<!-- /wp:separator -->

<!-- wp:archives {"displayAsDropdown":true,"showPostCounts":true} /-->
HTML
		);
	}
}
