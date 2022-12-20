<?php
/**
 * Class file for Test_Match_Blocks_Source
 *
 * (c) Alley <info@alley.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package wp-match-blocks
 */

namespace Alley\WP;

use Mantle\Testkit\Test_Case;

/**
 * Tests sources of blocks to `match_blocks()`.
 */
final class Test_Match_Blocks_Source extends Test_Case {
	/**
	 * Post IDs and objects are allowed.
	 */
	public function test_blocks_in_post() {
		$html = '<!-- wp:alley/foo /--><!-- wp:alley/bar /--><!-- wp:alley/baz /-->';
		$post = static::factory()->post->create_and_get( [ 'post_content' => $html ] );

		$this->assertSame( $html, serialize_blocks( match_blocks( $post ) ) );
		$this->assertSame( $html, serialize_blocks( match_blocks( $post->ID ) ) );
	}

	/**
	 * A single block instance should match its inner blocks.
	 */
	public function test_single_block_source() {
		$html   = '<!-- wp:foo --><!-- wp:bar /--><!-- wp:baz /--><!-- wp:bat /--><!-- /wp:foo -->';
		$blocks = parse_blocks( $html );
		$first  = array_shift( $blocks );

		$this->assertCount( 3, match_blocks( $first ) );

		$first = new \WP_Block_Parser_Block(
			$first['blockName'],
			$first['attrs'],
			$first['innerBlocks'],
			$first['innerHTML'],
			$first['innerContent'],
		);

		$this->assertCount( 3, match_blocks( $first ) );
	}

	/**
	 * Posts with no content should not return blocks.
	 */
	public function test_empty_source() {
		$this->assertEmpty( match_blocks( static::factory()->post->create( [ 'post_content' => '' ] ) ) );
	}

	/**
	 * Invalid post IDs should return no blocks.
	 */
	public function test_invalid_post() {
		$this->assertEmpty( match_blocks( $this->impossible_id ) );
	}

	/**
	 * Invalid source types should return no blocks.
	 */
	public function test_invalid_source() {
		$this->assertEmpty( match_blocks( false ) );
	}
}
