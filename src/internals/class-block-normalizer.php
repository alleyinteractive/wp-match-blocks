<?php
/**
 * Block_Normalizer class file. This class is not subject to semantic-versioning constraints
 *
 * (c) Alley <info@alley.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package wp-match-blocks
 */

namespace Alley\WP\Internals;

use Alley\WP\Blocks\Blocks;
use Alley\WP\Blocks\Parsed_Block;
use Alley\WP\Types\Single_Block;
use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizes an instance of the Single_Block interface.
 */
final class Block_Normalizer implements NormalizerInterface, DenormalizerInterface {
	/**
	 * Normalizes an object into a set of arrays/scalars.
	 *
	 * @throws InvalidArgumentException   Occurs when the object given is not a supported type for the normalizer.
	 * @throws CircularReferenceException Occurs when the normalizer detects a circular reference when no circular
	 *                                    reference handler can fix it.
	 * @throws LogicException             Occurs when the normalizer is not called in an expected context.
	 * @throws ExceptionInterface         Occurs for all the other cases of errors.
	 *
	 * @param mixed       $object  Object to normalize.
	 * @param string|null $format  Format the normalization result will be encoded as.
	 * @param array       $context Context options for the normalizer.
	 * @return array|string|int|float|bool|\ArrayObject|null \ArrayObject is used to make sure an empty object is
	 *                                                       encoded as an object not an array.
	 */
	public function normalize(
		mixed $object,
		?string $format = null,
		array $context = [],
	): array|string|int|float|bool|\ArrayObject|null {
		$pb = $object->parsed_block();

		return [
			'blockName'   => $object->block_name(),
			'attrs'       => $pb['attrs'],
			'innerBlocks' => Blocks::from_parsed_blocks( $pb['innerBlocks'] ),
			'innerHTML'   => $pb['innerHTML'],
			'origin'      => $object->serialized_blocks(),
		];
	}

	/**
	 * Checks whether the given class is supported for normalization by this normalizer.
	 *
	 * @param mixed       $data    Data to normalize.
	 * @param string|null $format  The format being (de-)serialized from or into.
	 * @param array       $context Context options for the normalizer.
	 */
	public function supportsNormalization( mixed $data, ?string $format = null, array $context = [] ): bool {
		return $data instanceof Single_Block && 'xml' === $format;
	}

	/**
	 * Returns the types potentially supported by this normalizer.
	 *
	 * For each supported formats (if applicable), the supported types should be
	 * returned as keys, and each type should be mapped to a boolean indicating
	 * if the result of supportsNormalization() can be cached or not
	 * (a result cannot be cached when it depends on the context or on the data.)
	 * A null value means that the normalizer does not support the corresponding
	 * type.
	 *
	 * Use type "object" to match any classes or interfaces,
	 * and type "*" to match any types.
	 *
	 * @param string|null $format The format being (de-)serialized from or into.
	 * @return array
	 */
	public function getSupportedTypes( ?string $format ): array {
		return [ '*' => true ];
	}

	/**
	 * Denormalizes data back into an object of the given class.
	 *
	 * @throws BadMethodCallException   Occurs when the normalizer is not called in an expected context.
	 * @throws InvalidArgumentException Occurs when the arguments are not coherent or not supported.
	 * @throws UnexpectedValueException Occurs when the item cannot be hydrated with the given data.
	 * @throws ExtraAttributesException Occurs when the item doesn't have attribute to receive given data.
	 * @throws LogicException           Occurs when the normalizer is not supposed to denormalize.
	 * @throws RuntimeException         Occurs if the class cannot be instantiated.
	 * @throws ExceptionInterface       Occurs for all the other cases of errors.
	 *
	 * @param mixed       $data    Data to restore.
	 * @param string      $type    The expected class to instantiate.
	 * @param string|null $format  Format the given data was extracted from.
	 * @param array       $context Options available to the denormalizer.
	 * @return Single_Block
	 */
	public function denormalize( mixed $data, string $type, ?string $format = null, array $context = [] ): mixed {
		$origin = parse_blocks( $data['origin'] );

		return new Parsed_Block( $origin[0] );
	}

	/**
	 * Checks whether the given class is supported for denormalization by this normalizer.
	 *
	 * @param mixed       $data    Data to denormalize from.
	 * @param string      $type    The class to which the data should be denormalized.
	 * @param string|null $format  The format being deserialized from.
	 * @param array       $context Context options for the normalizer.
	 * @return bool
	 */
	public function supportsDenormalization(
		mixed $data,
		string $type,
		?string $format = null,
		array $context = []
	): bool {
		return Single_Block::class === $type && 'xml' === $format;
	}
}
