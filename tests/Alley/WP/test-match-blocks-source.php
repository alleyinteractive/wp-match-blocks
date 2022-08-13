<?php
/**
 * Class file for Test_Match_Blocks_Source
 *
 * (c) Alley <info@alley.co>
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
