<?php
/**
 * Class file for MatchBlocksAttrsTest
 *
 * (c) Alley <info@alley.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package wp-match-blocks
 */

namespace Alley\WP\Tests\Unit;

use Mantle\Testkit\Test_Case;

use function Alley\WP\match_blocks;

/**
 * Tests the `attrs` parameter to `match_blocks()`.
 */
final class MatchBlocksAttrsTest extends Test_Case {
	/**
	 * Example block HTML.
	 *
	 * @var string
	 */
	private const ARCHIVES = '<!-- wp:archives {"displayAsDropdown":true,"showPostCounts":true} /-->';

	/**
	 * Example block HTML.
	 *
	 * @var string
	 */
	private const EMBED = <<<HTML
<!-- wp:core-embed/vimeo {"url":"https://vimeo.com/22439234","type":"video","providerNameSlug":"vimeo","align":"wide","className":"wp-has-aspect-ratio wp-embed-aspect-16-9"} -->
<figure class="wp-block-embed-vimeo alignwide wp-block-embed is-type-video is-provider-vimeo wp-has-aspect-ratio wp-embed-aspect-16-9">
    <div class="wp-block-embed__wrapper">https://vimeo.com/22439234</div>
</figure>
<!-- /wp:core-embed/vimeo -->
HTML;

	/**
	 * A block should match this `attrs` clause.
	 */
	public function test_one_clause_match() {
		$this->assertSame(
			parse_blocks( self::EMBED ),
			match_blocks(
				self::EMBED,
				[
					'attrs' => [
						[
							'key'   => 'type',
							'value' => 'video',
						],
					],
				]
			)
		);
	}

	/**
	 * No block should match this `attrs` clause.
	 */
	public function test_one_clause_not_match() {
		$this->assertEmpty(
			match_blocks(
				self::EMBED,
				[
					'attrs' => [
						[
							'key'      => 'type',
							'value'    => 'video',
							'operator' => '!==',
						],
					],
				]
			)
		);
	}

	/**
	 * A block should match these `attrs` `AND` clauses.
	 */
	public function test_multiple_clauses_match_and() {
		$this->assertSame(
			parse_blocks( self::EMBED ),
			match_blocks(
				self::EMBED,
				[
					'attrs' => [
						'relation' => 'AND',
						[
							'key'   => 'type',
							'value' => 'video',
						],
						[
							'key'   => 'providerNameSlug',
							'value' => 'vimeo',
						],
					],
				]
			)
		);
	}

	/**
	 * No block should match these `attrs` `AND` clauses.
	 */
	public function test_multiple_clauses_not_match_and() {
		$this->assertEmpty(
			match_blocks(
				self::EMBED,
				[
					'attrs' => [
						'relation' => 'AND',
						[
							'key'   => 'type',
							'value' => 'video',
						],
						[
							'key'   => 'providerNameSlug',
							'value' => 'youtube',
						],
					],
				]
			)
		);
	}

	/**
	 * A block should match these `attrs` `OR` clauses.
	 */
	public function test_multiple_clauses_match_or() {
		$this->assertSame(
			parse_blocks( self::EMBED ),
			match_blocks(
				self::EMBED,
				[
					'attrs' => [
						'relation' => 'OR',
						[
							'key'   => 'type',
							'value' => 'video',
						],
						[
							'key'   => 'providerNameSlug',
							'value' => 'youtube',
						],
					],
				]
			)
		);
	}

	/**
	 * No block should match these `attrs` `OR` clauses.
	 */
	public function test_multiple_clauses_not_match_or() {
		$this->assertEmpty(
			match_blocks(
				self::EMBED,
				[
					'attrs' => [
						'relation' => 'OR',
						[
							'key'   => 'type',
							'value' => 'image',
						],
						[
							'key'   => 'providerNameSlug',
							'value' => 'youtube',
						],
					],
				]
			)
		);
	}

	/**
	 * Blocks should match these nested `attrs` clauses.
	 */
	public function test_subquery_match() {
		$content = self::EMBED . self::ARCHIVES;

		$this->assertSame(
			parse_blocks( $content ),
			match_blocks(
				$content,
				[
					'attrs' => [
						'relation' => 'OR',
						[
							'key'   => 'type',
							'value' => 'video',
						],
						[
							'relation' => 'OR',
							[
								'key'   => 'providerNameSlug',
								'value' => 'youtube',
							],
							[
								'key'   => 'align',
								'value' => 'left',
							],
							[
								'key'   => 'displayAsDropdown',
								'value' => true,
							],
						],
					],
				]
			)
		);
	}

	/**
	 * No blocks should match these nested `attrs` clauses.
	 */
	public function test_subquery_not_match() {
		$this->assertEmpty(
			match_blocks(
				self::EMBED,
				[
					'attrs' => [
						'relation' => 'OR',
						[
							'key'   => 'type',
							'value' => 'image',
						],
						[
							'relation' => 'OR',
							[
								'key'   => 'providerNameSlug',
								'value' => 'youtube',
							],
							[
								'key'   => 'align',
								'value' => 'left',
							],
						],
					],
				]
			)
		);
	}
}
