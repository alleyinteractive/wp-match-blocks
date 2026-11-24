# Changelog

This library adheres to [Semantic Versioning](https://semver.org/) and [Keep a CHANGELOG](https://keepachangelog.com/en/1.0.0/).

## Unreleased

Nothing yet.

## 5.0.0

### Changed

- Require `alleyinteractive/laminas-validator-extensions` ^3.0 and Laminas Validator 3, which adds PHP 8.6 support.
- Require `alleyinteractive/wp-type-extensions` ^5.1.
- The minimum PHP version is now 8.4.
- Subclasses of `Block_Validator` that declare `$messageTemplates` or `$messageVariables` must type them as `array`, and any `setValue()` override must declare a `void` return type.
- Laminas Validator Extensions 3 changed several of its own validators, such as renaming the `compared` option of `Comparison` to `target`. Code that uses those validators directly alongside this package should follow [its upgrade notes](https://github.com/alleyinteractive/laminas-validator-extensions/blob/main/CHANGELOG.md#300).

`match_blocks()` and the options accepted by this package's validators are unchanged, and `setOptions()` remains available on `Block_Validator`.

## 4.4.0

### Added

- `Matched_Blocks` now implements the `Countable` interface.
- Support for `alleyinteractive/wp-type-extensions` v5.

## 4.3.0

### Added

- `__experimental_xpath` parameter for matching blocks by XPath queries.
- `Matched_Blocks` implementation of the `Alley\WP\Types\Serialized_Blocks` interface from the [Type Extensions](https://github.com/alleyinteractive/wp-type-extensions/) library.

## 4.2.0

### Removed

- Reverted changes from version 4.1 to avoid conflicts with the Alleyvate plugin.

## 4.1.0 [YANKED]

### Added

- `__experimental_xpath` parameter for matching blocks by XPath queries.
- `Matched_Blocks` implementation of the `Alley\WP\Types\Serialized_Blocks` interface from the [Type Extensions](https://github.com/alleyinteractive/wp-type-extensions/) library.

## 4.0.0

### Changed

- The minimum PHP version is now 8.2.

## 3.1.0

### Changed

- Reduce uses of validators within validators.

## 3.0.0

### Changed

- "Classic" blocks, which have inner HTML but no block name, are no longer considered empty.

## 2.0.1

### Fixed

- Missing `alleyinteractive/composer-wordpress-autoloader` dependency.

## 2.0.0

### Added

- `with_innerhtml` parameter for matching blocks by their inner HTML. Includes companion `\Alley\WP\Validator\Block_InnerHTML` validator.
- `has_innerblocks` parameter for matching blocks by whether they have inner blocks. Includes companion `\Alley\WP\Validator\Block_InnerBlocks_Count` validator.
- `is_valid` parameter for matching blocks by custom validation logic.
- `CONTAINS` and `NOT CONTAINS` (case-sensitive), and `LIKE` and `NOT LIKE` (case-insensitive) operators to `attrs` parameter.

### Changed

- Passing a single block instance will return matches within its inner blocks.

### Removed

- PHP 7.4 support.

## 1.0.1

### Fixed

- Incorrect namespace in `README.md` examples.

## 1.0.0

Initial release.
