<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
 */

/**
 * Plugin constants.
 */
define('ADMIN_PLUGIN', dirname(__DIR__) . '/');

if (!defined('USER_MODEL')) {
	define('USER_MODEL', 'User');
}

/**
 * Current version.
 */
Configure::write('Admin.version', file_get_contents(ADMIN_PLUGIN . 'version.md'));

/**
 * Name of the application.
 */
Configure::write('Admin.appName', __('Admin'));

/**
 * Pseudo plugin name to wrap application models in.
 */
Configure::write('Admin.coreName', 'Core');

/**
 * Alias of the administrator ARO.
 */
Configure::write('Admin.adminAlias', 'administrator');

/**
 * Ignore/restrict these models.
 */
Configure::write('Admin.ignoreModels', array());

/**
 * Default settings for each model.
 */
Configure::write('Admin.modelDefaults', array(
	'imageFields' => array('image'),
	'fileFields' => array('image', 'file'),
	'hideFields' => array(),
	'paginateLimit' => 25,
	'associationLimit' => 75,
	'batchDelete' => true,
	'deletable' => true,
	'icon' => ''
));
