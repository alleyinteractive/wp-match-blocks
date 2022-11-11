<?php
/**
 * Block_InnerBlocks_Count class file
 *
 * (c) Alley <info@alley.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package wp-match-blocks
 */

namespace Alley\WP\Validator;

use Alley\Validator\Comparison;
use Laminas\Validator\Exception\InvalidArgumentException;
use Laminas\Validator\ValidatorInterface;
use Traversable;
use WP_Block;
use WP_Block_Parser_Block;
use WP_Error;

/**
 * Validates whether the given block has a number of inner blocks.
 */
final class Block_InnerBlocks_Count extends Block_Validator {
	/**
	 * Array of validation failure message templates.
	 *
	 * @var string[]
	 */
	protected $messageTemplates = [
		'not_equal'                    => '',
		'not_identical'                => '',
		'is_equal'                     => '',
		'is_identical'                 => '',
		'not_less_than'                => '',
		'not_greater_than'             => '',
		'not_less_than_or_equal_to'    => '',
		'not_greater_than_or_equal_to' => '',
		'default'                      => '',
	];

	/**
	 * Map of \Alley\Validator\Comparison message templates to this validator's messages.
	 *
	 * @var string[]
	 */
	private $parent_message_templates = [
		'notEqual'                => 'not_equal',
		'notIdentical'            => 'not_identical',
		'isEqual'                 => 'is_equal',
		'isIdentical'             => 'is_identical',
		'notLessThan'             => 'not_less_than',
		'notGreaterThan'          => 'not_greater_than',
		'notLessThanOrEqualTo'    => 'not_less_than_or_equal_to',
		'notGreaterThanOrEqualTo' => 'not_greater_than_or_equal_to',
	];

	/**
	 * Array of additional variables available for validation failure messages.
	 *
	 * @var string[]
	 */
	protected $messageVariables = [
		'count' => [
			'options' => 'count',
		],
	];

	/**
	 * Options for this validator.
	 *
	 * @var array
	 */
	protected $options = [
		'operator' => '>=',
		'count'    => 0,
	];

	/**
	 * Valid inner block counts based on options.
	 *
	 * @var ValidatorInterface
	 */
	private ValidatorInterface $valid_comparisons;

	/**
	 * Set up.
	 *
	 * @param array|Traversable $options Validator options.
	 */
	public function __construct( $options = null ) {
		$this->messageTemplates = [
			'not_equal'                    => sprintf(
				/* translators: 1: expected count placeholder, 2: actual count placeholder */
				__( 'Number of inner blocks must be %1$s but is %2$s.', 'alley' ),
				'%count%',
				'%value%',
			),
			'not_identical'                => sprintf(
				/* translators: 1: expected count placeholder, 2: actual count placeholder */
				__( 'Number of inner blocks must be %1$s but is %2$s.', 'alley' ),
				'%count%',
				'%value%',
			),
			'is_equal'                     => sprintf(
				/* translators: 1: expected count placeholder */
				__( 'Number of inner blocks must not be %1$s.', 'alley' ),
				'%count%',
			),
			'is_identical'                 => sprintf(
				/* translators: 1: expected count placeholder */
				__( 'Number of inner blocks must not be %1$s.', 'alley' ),
				'%count%',
			),
			'not_less_than'                => sprintf(
				/* translators: 1: expected count placeholder, 2: actual count placeholder */
				__( 'Number of inner blocks must be less than %1$s but is %2$s.', 'alley' ),
				'%count%',
				'%value%',
			),
			'not_greater_than'             => sprintf(
				/* translators: 1: expected count placeholder, 2: actual count placeholder */
				__( 'Number of inner blocks must be greater than %1$s but is %2$s.', 'alley' ),
				'%count%',
				'%value%',
			),
			'not_less_than_or_equal_to'    => sprintf(
				/* translators: 1: expected count placeholder, 2: actual count placeholder */
				__( 'Number of inner blocks must be less than or equal to %1$s but is %2$s.', 'alley' ),
				'%count%',
				'%value%',
			),
			'not_greater_than_or_equal_to' => sprintf(
				/* translators: 1: expected count placeholder, 2: actual count placeholder */
				__( 'Number of inner blocks must be greater than or equal to %1$s but is %2$s.', 'alley' ),
				'%count%',
				'%value%',
			),
			'default'                      => __( 'Invalid number of inner blocks.', 'alley' ),
		];

		parent::__construct( $options );
	}

	/**
	 * Sets one or multiple options. Refreshes the cached validators for the comparisons.
	 *
	 * @throws InvalidArgumentException If requested comparisons are invalid.
	 *
	 * @param array|Traversable $options Options to set.
	 * @return self
	 */
	public function setOptions( $options = [] ) {
		parent::setOptions( $options );

		$this->valid_comparisons = new Comparison(
			[
				'operator' => $this->options['operator'],
				'compared' => $this->options['count'],
			],
		);

		return $this;
	}

	/**
	 * Apply block validation logic and add any validation errors.
	 *
	 * @param WP_Block_Parser_Block $block The block to test.
	 */
	protected function test_block( WP_Block_Parser_Block $block ): void {
		$count = \count( $block->innerBlocks );

		if ( ! $this->valid_comparisons->isValid( $count ) ) {
			$message_keys = array_keys( $this->valid_comparisons->getMessages() );

			foreach ( $message_keys as $key ) {
				$message = 'default';

				if ( isset( $this->parent_message_templates[ $key ] ) ) {
					$message = $this->parent_message_templates[ $key ];
				}

				$this->error( $message, $count );
			}
		}
	}

	/**
	 * Sets the 'count' option.
	 *
	 * @param int $count Option.
	 */
	protected function setCount( $count ) {
		$this->options['count'] = (int) $count;
	}
}
