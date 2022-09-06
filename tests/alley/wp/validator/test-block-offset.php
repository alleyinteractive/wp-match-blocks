<?php
/**
 * Class file for Test_Block_Offset
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
 * Unit tests for the block offset validator.
 */
final class Test_Block_Offset extends Test_Case {
	/**
	 * Test that valid input with the given options is marked as valid.
	 *
	 * @dataProvider data_valid_input
	 *
	 * @param mixed $input   Input.
	 * @param array $options Options.
	 */
	public function test_valid_input( $input, $options ) {
		$validator = new Block_Offset( $options );
		$this->assertTrue( $validator->isValid( $input ) );
	}

	/**
	 * Data provider for testing valid input.
	 *
	 * @return array
	 */
	public function data_valid_input() {
		$blocks = $this->blocks();

		return [
			[
				$blocks[0],
				[
					'blocks'            => $this->blocks(),
					'offset'            => 0,
					'skip_empty_blocks' => true,
				],
			],
			[
				$blocks[2],
				[
					'blocks'            => $this->blocks(),
					'offset'            => 1,
					'skip_empty_blocks' => true,
				],
			],
			[
				$blocks[2],
				[
					'blocks'            => $this->blocks(),
					'offset'            => 2,
					'skip_empty_blocks' => false,
				],
			],
			[
				$blocks[6],
				[
					'blocks'            => $this->blocks(),
					'offset'            => -2,
					'skip_empty_blocks' => true,
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
		$validator = new Block_Offset( $options );
		$this->assertFalse( $validator->isValid( $input ) );
		$this->assertSame( $messages, $validator->getMessages() );
	}

	/**
	 * Data provider for testing invalid input.
	 *
	 * @return array
	 */
	public function data_invalid_input() {
		$blocks = $this->blocks();

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
					'blocks'            => $this->blocks(),
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
					'blocks'            => $this->blocks(),
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
					'blocks'            => $this->blocks(),
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
					'blocks'            => $this->blocks(),
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
					'blocks'            => $this->blocks(),
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
	protected function blocks() {
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
