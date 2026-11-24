<?php
/**
 * Block_Validator class file
 *
 * (c) Alley <info@alley.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package wp-match-blocks
 */

namespace Alley\WP\Validator;

use Alley\Validator\ExtendedAbstractValidator;
use Laminas\Validator\Exception\InvalidArgumentException;
use Traversable;
use WP_Block;
use WP_Block_Parser_Block;

/**
 * Abstract class for validating WordPress blocks.
 */
abstract class Block_Validator extends ExtendedAbstractValidator {
	/**
	 * Properties that need to be in the submitted block to convert it into a parsed block.
	 *
	 * @var string[]
	 */
	private const REQUIRED_BLOCK_KEYS = [ 'blockName', 'attrs', 'innerBlocks', 'innerHTML', 'innerContent' ];

	/**
	 * Options handled by the Laminas constructor rather than by this validator.
	 *
	 * @var string[]
	 */
	private const LAMINAS_OPTIONS = [ 'messages', 'translator', 'translatorEnabled', 'translatorTextDomain', 'valueObscured' ];

	/**
	 * Options for this validator.
	 *
	 * @var mixed[]
	 */
	protected $options = [];

	/**
	 * Set up.
	 *
	 * @phpstan-param array<string, mixed>|Traversable<string, mixed>|null $options
	 *
	 * @param array|Traversable|null $options Validator options.
	 */
	public function __construct( $options = null ) {
		if ( $options instanceof Traversable ) {
			$options = iterator_to_array( $options );
		}

		if ( ! \is_array( $options ) ) {
			$options = [];
		}

		$laminas_options = array_intersect_key( $options, array_flip( self::LAMINAS_OPTIONS ) );

		parent::__construct( $laminas_options ); // @phpstan-ignore argument.type

		$this->setOptions( array_diff_key( $options, $laminas_options ) );
	}

	/**
	 * Sets one or multiple options, calling a matching `set*()` method for each option where one exists.
	 *
	 * @phpstan-param array<string, mixed>|Traversable<string, mixed> $options
	 *
	 * @param array|Traversable $options Options to set.
	 * @return static
	 */
	public function setOptions( $options = [] ) {
		foreach ( $options as $name => $option ) {
			$setter = 'set' . ucfirst( $name );

			if ( 'setOptions' !== $setter && method_exists( $this, $setter ) ) {
				$this->{$setter}( $option );
			} else {
				$this->options[ $name ] = $option;
			}
		}

		return $this;
	}

	/**
	 * Apply block validation logic and add any validation errors.
	 *
	 * @param WP_Block_Parser_Block $block The block to test.
	 */
	abstract protected function test_block( WP_Block_Parser_Block $block ): void;

	/**
	 * Apply validation logic and add any validation errors.
	 *
	 * @param WP_Block_Parser_Block $value The value to test.
	 */
	final protected function testValue( $value ): void {
		$this->test_block( $value );
	}

	/**
	 * Converts the input into a parser block instance to be validated.
	 *
	 * @throws InvalidArgumentException If the submitted value can't be parsed into a block.
	 *
	 * @phpstan-param WP_Block|WP_Block_Parser_Block|array{
	 *     blockName: ?string,
	 *     attrs: array<string, mixed>,
	 *     innerBlocks: mixed[],
	 *     innerHTML: string,
	 *     innerContent: string[]
	 * } $value
	 *
	 * @param array|WP_Block|WP_Block_Parser_Block $value Original block.
	 */
	protected function setValue( $value ): void {
		$value = self::to_parser_block( $value );

		parent::setValue( $value );
	}

	/**
	 * Format an option for use in a failure message, rendering lists as "[a, b]".
	 *
	 * @param mixed $value Option value.
	 * @return string
	 */
	protected static function message_value( mixed $value ): string {
		if ( \is_array( $value ) ) {
			return '[' . implode( ', ', array_map( [ self::class, 'message_value' ], $value ) ) . ']';
		}

		return \is_scalar( $value ) || null === $value ? (string) $value : get_debug_type( $value );
	}

	/**
	 * Convert a block or a representation thereof to a parser block object.
	 *
	 * @throws InvalidArgumentException If input cannot be parsed.
	 *
	 * @phpstan-param WP_Block|WP_Block_Parser_Block|array{
	 *     blockName: ?string,
	 *     attrs: array<string, mixed>,
	 *     innerBlocks: mixed[],
	 *     innerHTML: string,
	 *     innerContent: string[]
	 * } $value
	 *
	 * @param array|WP_Block|WP_Block_Parser_Block $value Original block.
	 * @return WP_Block_Parser_Block Block instance.
	 */
	protected static function to_parser_block( $value ): WP_Block_Parser_Block {
		if ( $value instanceof WP_Block ) {
			$value = $value->parsed_block;
		}

		if ( $value instanceof WP_Block_Parser_Block ) {
			$value = (array) $value;
		}

		$actual_keys = array_keys( $value );

		if ( array_diff( self::REQUIRED_BLOCK_KEYS, $actual_keys ) ) {
			throw new InvalidArgumentException( esc_html__( 'Cannot parse block from input.', 'alley' ) );
		}

		$value = new WP_Block_Parser_Block(
			$value['blockName'],
			$value['attrs'],
			$value['innerBlocks'],
			$value['innerHTML'],
			$value['innerContent'],
		);

		return $value;
	}
}
