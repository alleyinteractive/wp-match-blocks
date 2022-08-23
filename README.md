# Match Blocks

`match_blocks()` selects the blocks in post content, or a given set of blocks or block HTML, that match the given criteria, such as the block name, block attributes, or position within the set of blocks.

Blocks can be matched by:

* Block name or names (`name`)
* Block attributes (`atts`, `with_atts`)
* The block's positive or negative index within the set (`position`)
* Whether the block represents only space (`skip_empty_blocks`)

Passing matching parameters is optional; all non-empty blocks match by default.

Additionally:

* Recursion into inner blocks is supported (`flatten`).
* The set of matching blocks can be limited by size (`limit`) or their position in the set of matches (`nth_of_type`).
* The number of matches can be returned instead of the matched blocks (`count`).
* The companion `match_block()` function reduces the filtered set of results to a single parsed block.

`match_blocks()` is powered by a set of block validation classes that utilize the [Laminas Validator](https://docs.laminas.dev/laminas-validator/) framework and [Laminas Validator Extensions](https://github.com/alleyinteractive/laminas-validator-extensions) package. These validators, along with a base class for validating blocks, are included here. [See the validators section for their documentation](#validators).

## Examples

Find all paragraph blocks in a post:

```php
<?php

$grafs = \Alley\match_blocks( $post, [ 'name' => 'core/paragraph' ] );
```

Include paragraphs in inner blocks:

```php
<?php

$grafs = \Alley\match_blocks(
    $post,
    [
        'flatten' => true,
        'name'    => 'core/paragraph',
    ]
);
```

Get the number of paragraph blocks:

```php
<?php

$count = \Alley\match_blocks(
    $post,
    [
        'count' => true,
        'name'  => 'core/paragraph',
    ]
);
```

Get all paragraphs and headings:

```php
<?php

$blocks = \Alley\match_blocks(
    '<!-- wp:paragraph -->...',
    [
        'name' => [ 'core/heading', 'core/paragraph' ],
    ]
);
```

Get only paragraphs that have been explicitly aligned:

```php
<?php

$blocks = \Alley\match_blocks(
    [ /* blocks */ ],
    [
        'name'       => 'core/paragraph',
        'with_attrs' => 'align',
    ]
);
```

Get only paragraphs that have been right-aligned:

```php
<?php

$blocks = \Alley\match_blocks(
    $post,
    [
        'attrs' => [
            [
                'key'   => 'align',
                'value' => 'right',
            ],
        ],
        'name'  => 'core/paragraph',
    ]
);
```

Get only paragraphs that have been aligned, but not to the right:

```php
<?php

$blocks = \Alley\match_blocks(
    $post,
    [
        'attrs' => [
            [
                'key'      => 'align',
                'value'    => 'right',
                'operator' => '!==',
            ],
        ],
        'name'  => 'core/paragraph',
    ]
);
```

Get only paragraphs that have been aligned to the left or the right:

```php
<?php

$blocks = \Alley\match_blocks(
    $post,
    [
        'attrs' => [
            [
                'key'      => 'align',
                'value'    => [ 'left', 'right' ],
                'operator' => 'IN',
            ],
        ],
        'name'  => 'core/paragraph',
    ]
);
```

Get all images credited to the Associated Press:

```php
<?php

$grafs = \Alley\match_blocks(
    $post,
    [
        'attrs' => [
            [
                'key'      => 'credit',
                'value'    => '/(The )?Associated Press/i',
                'operator' => 'REGEX',
            ],
            [
                'key'   => 'credit',
                'value' => 'AP',
            ],
            'relation' => 'OR',
        ],
        'name'  => 'core/image',
    ]
);
```

Get the first three blocks:

```php
<?php

$blocks = \Alley\match_blocks(
    $post,
    [
        'limit' => 3,
    ]
);
```

Get the first three paragraph blocks:

```php
<?php

$blocks = \Alley\match_blocks(
    $post,
    [
        'limit' => 3,
        'name'  => 'core/paragraph',
    ]
);
```

Get the third paragraph:

```php
<?php

$blocks = \Alley\match_blocks(
    $post,
    [
        'name'        => 'core/paragraph',
        'nth_of_type' => 3,
    ]
);

// Or, skip straight to the parsed block:

$block = \Alley\match_block(
    $post,
    [
        'name'        => 'core/paragraph',
        'nth_of_type' => '3n',
    ]
);
```

Get every third paragraph:

```php
<?php

$blocks = \Alley\match_blocks(
    $post,
    [
        'name'        => 'core/paragraph',
        'nth_of_type' => '3n',
    ]
);
```

Get paragraphs 3-8:

```php
<?php

$blocks = \Alley\match_blocks(
    $post,
    [
        'name'        => 'core/paragraph',
        'nth_of_type' => [ 'n+3', '-n+8' ],
    ]
);
```

Get the block at position 3 in the set if it's a paragraph:

```php
<?php

$blocks = \Alley\match_blocks(
    $post,
    [
        'name'     => 'core/paragraph',
        'position' => 3,
    ]
);
```

Get the last two blocks:

```php
<?php

$blocks = \Alley\match_blocks(
    $post,
    [
        'position' => [ -1, -2 ],
    ]
);
```

Get all non-empty blocks:

```php
<?php

$blocks = \Alley\match_blocks( $post );
```

Get all empty blocks:

```php
<?php

$blocks = \Alley\match_blocks(
    $post,
    [
        'name'              => null,
        'skip_empty_blocks' => false,
    ]
);
```

## Validators

This package provides classes for validating blocks based on the [Laminas Validator](https://docs.laminas.dev/laminas-validator/) framework and [Laminas Validator Extensions](https://github.com/alleyinteractive/laminas-validator-extensions) package, plus a custom base block validation class.

### Base Validator

The abstract `Alley\WP\Validator\BlockValidator` class extends `Alley\Validator\BaseValidator` and, much like `BaseValidator`, standardizes validation of blocks.

When extending `BlockValidator`, validation logic goes into a `test_block()` method. `test_block()` always receives a `\WP_Block_Parser_Block` instance; validation will automatically fail if the input is not an instance of `\WP_Block`, `\WP_Block_Parser_Block`, or an array representation of a block.

### `Block_Attribute`

`Alley\WP\Validator\Block_Attribute` validates whether the block contains, or does not contain, the specified attribute name, value, or name-value pair.

The block passes if a name comparison is specified, and the block contains an attribute whose name matches the comparison; if a value comparison is specified, and the block contains an attribute whose value matches the comparison; or if both name and value comparisons are specified, and the block contains an attribute with a matching name and value.

#### Supported options

The following options are supported for `Alley\WP\Validator\Block_Attribute`:

- `key`: The name of a block attribute, or an array of names, or a regular expression pattern. Default none.
- `value`: A block attribute value, or an array of values, or regular expression pattern. Default none.
- `operator`: The operator with which to compare `$value` to block attributes. Accepts `IN`, `NOT IN`, `REGEXP`, `NOT REGEXP`, or any operator supported by `\Alley\Validator\Comparison`. Default is `===`.
- `key_operator`: Equivalent to `operator` but for `$key`.

#### Basic usage

```php
<?php

// '<!-- wp:media-text {"mediaId":617,"mediaType":"image","isStackedOnMobile":false,"className":"alignwide"} -->';

$valid = new Alley\WP\Validator\Block_Attribute(
    [
        'key'   => 'mediaType',
        'value' => 'image',
    ],
);

$valid = new Alley\WP\Validator\Block_Attribute(
    [
        'key'          => [ 'mediaType', 'mediaId' ],
        'key_operator' => 'IN',
    ],
);

$valid = new Alley\WP\Validator\Block_Attribute(
    [
        'key'          => '/^media/',
        'key_operator' => 'REGEX',
        'value'        => [ 'image', 'video' ],
        'operator'     => 'IN',
    ],
);

$valid = new Alley\WP\Validator\Block_Attribute(
    [
        'key'          => '/^media/',
        'key_operator' => 'REGEX',
        'value'        => [ 'audio', 'document' ],
        'operator'     => 'NOT IN',
    ],
);
```

### `Block_Name`

`Alley\WP\Validator\Block_Name` validates whether a block has a given name or one of a set of names. The block passes validation if the block name is in the set.

#### Supported options

The following options are supported for `Alley\WP\Validator\Block_Name`:

-`name`: The block name or names.

#### Basic usage

```php
<?php

$valid = new Alley\WP\Validator\Block_Name(
    [
        'name' => 'core/paragraph',
    ]
);

$valid = new Alley\WP\Validator\Block_Name(
    [
        'name' => [ 'core/gallery', 'jetpack/slideshow', 'jetpack/tiled-gallery' ],
    ]
);
```

### `Block_Offset`

`Alley\WP\Validator\Block_Offset` validates whether the block appears at one of the given numeric offsets within a list of blocks.

The block matches if it appears at one of the offsets in the list.

Identity is determined by comparing the `\WP_Block_Parser_Block` instances as arrays.

#### Supported options

The following options are supported for `Alley\WP\Validator\Block_Offset`:

- `blocks`: An array or iterable of blocks.
- `offset`: The expected offset or offsets. Negative offsets count from the end of the list.
- `skip_empty_blocks`: Whether to skip blocks that are "empty" according to the `Nonempty_Block_Validator` when indexing `$blocks`. Default true.

#### Basic usage

```php
<?php

$blocks = parse_blocks(
    <<<HTML
<!-- wp:paragraph --><p>Hello, world!</p><!-- /wp:paragraph -->

<!-- wp:archives {\"displayAsDropdown\":true,\"showPostCounts\":true} /-->

<!-- wp:media-text {\"mediaId\":617,\"mediaType\":\"image\",\"isStackedOnMobile\":false,\"className\":\"alignwide\"} -->
HTML
);

$valid = new Alley\WP\Validator\Block_Offset(
    [
        'blocks' => $blocks,
        'offset' => 1,
    ],
);
$valid->isValid( $blocks[2] ); // true

$valid = new Alley\WP\Validator\Block_Offset(
    [
        'blocks'            => $blocks,
        'offset'            => [ 4 ],
        'skip_empty_blocks' => false,
    ],
);
$valid->isValid( $blocks[4] ); // true

$valid = new Alley\WP\Validator\Block_Offset(
    [
        'blocks' => $blocks,
        'offset' => -2,
    ],
);
$valid->isValid( $blocks[2] ); // true
```

### `Nonempty_Block`

`Alley\WP\Validator\Nonempty_Block` validates that the given block is not "empty" -- for example, not a block representing only line breaks.

The block passes validation if it has a non-null name.

#### Supported options

None.

#### Basic usage

```php
<?php

$blocks = parse_blocks( "\n" );

$valid = new \Alley\WP\Validator\Nonempty_Block();
$valid->isValid( $blocks[0] ); // false
```