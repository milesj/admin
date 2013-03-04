<?php


/**
 * Plugin constants.
 */
define('ADMIN_PLUGIN', dirname(__DIR__) . '/');

/**
 * Current version.
 */
Configure::write('Admin.version', file_get_contents(dirname(__DIR__) . '/version.md'));

Configure::write('Admin.app', 'Admin');

Configure::write('Admin.settings', array(
	'imageFields' => array('image'),
	'fileFields' => array('image', 'file'),
	'hideFields' => array(),
	'paginateLimit' => 25,
	'associationLimit' => 75,
	'batchDelete' => true,
	'deletable' => true
));
