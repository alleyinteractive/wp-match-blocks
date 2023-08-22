<?php
/**
 * Nonempty_Block class file
 *
 * (c) Alley <info@alley.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package wp-match-blocks
 */

namespace Alley\WP\Validator;

use Alley\Validator\AnyValidator;
use Alley\Validator\Not;
use Laminas\Validator\ValidatorInterface;
use Traversable;
use WP_Block_Parser_Block;

/**
 * Validates that the given block is not "empty" -- for example, not a block representing only line breaks.
 */
final class Nonempty_Block extends Block_Validator {
	/**
	 * Error code.
	 *
	 * @var string
	 */
	public const EMPTY_BLOCK = 'empty_block';

	/**
	 * Array of validation failure message templates.
	 *
	 * @var string[]
	 */
	protected $messageTemplates = [
		self::EMPTY_BLOCK => '',
	];

	/**
	 * Validates the input block.
	 *
	 * @var ValidatorInterface
	 */
	private ValidatorInterface $nonempty_tests;

	/**
	 * Set up.
	 *
	 * @param array|Traversable $options Validator options.
	 */
	public function __construct( $options = null ) {
		$this->messageTemplates[ self::EMPTY_BLOCK ] = __( 'Block is empty.', 'alley' );

		$this->nonempty_tests = new AnyValidator(
			[
				new Not(
					new Block_Name(
						[
							'name' => null,
						],
					),
					__( 'Block name is null.', 'alley' ),
				),
				new Block_InnerHTML(
					[
						'operator' => 'REGEX',
						'content'  => '#\S#',
					],
				),
			],
		);

		parent::__construct( $options );
	}

	/**
	 * Apply block validation logic and add any validation errors.
	 *
	 * @param WP_Block_Parser_Block $block The block to test.
	 */
	protected function test_block( WP_Block_Parser_Block $block ): void {
		if ( ! $this->nonempty_tests->isValid( $block ) ) {
			$this->error( self::EMPTY_BLOCK );
		}
	}
}
