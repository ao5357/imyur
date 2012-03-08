<?php if (!class_exists('CFRuntime')) die('No direct access allowed.');
/**
 * Stores your AWS account information. Add your account information, and then rename this file
 * to 'config.inc.php'.
 *
 * @version 2011.12.14
 * @license See the included NOTICE.md file for more information.
 * @copyright See the included NOTICE.md file for more information.
 * @link http://aws.amazon.com/php/ PHP Developer Center
 * @link http://aws.amazon.com/security-credentials AWS Security Credentials
 */

/**
 * Create a list of credential sets that can be used with the SDK.
 */
CFCredentials::set(array(
	'@default' => array(
		'key' => 'AKIAIQ246I2QNP5GL4TQ',
		'secret' => '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',
		'default_cache_config' => 'apc',
		'certificate_authority' => false
		)
	)
);