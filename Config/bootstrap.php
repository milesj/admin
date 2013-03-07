<?php

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
 * Plugin settings.
 */
Configure::write('Admin.appName', __('Admin'));

Configure::write('Admin.coreName', 'Core');

Configure::write('Admin.adminAlias', 'administrator');

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
	'deletable' => true
));
