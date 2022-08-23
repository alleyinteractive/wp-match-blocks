<?php
/**
 * PHP-CS-Fixer configuration
 *
 * (c) Alley <info@alley.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package wp-match-blocks
 */

$finder = PhpCsFixer\Finder::create()->in(
	[
		__DIR__ . '/src/',
		__DIR__ . '/tests/',
	]
);

$config = new PhpCsFixer\Config();
$config->setRules(
	[
		'@PHP81Migration'       => true,
		'method_argument_space' => false, // Enabled by '@PHP81Migration' but generates invalid spacing for WordPress.
		'heredoc_indentation'   => false, // Enabled by '@PHP81Migration' but generates heredoc with syntax errors.

		'final_class'                             => true,
		'native_constant_invocation'              => true,
		'native_function_casing'                  => true,
		'native_function_invocation'              => true,
		'native_function_type_declaration_casing' => true,
	]
);
$config->setFinder( $finder );

return $config;
