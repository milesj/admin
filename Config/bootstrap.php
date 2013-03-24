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

// User model
if (!defined('USER_MODEL')) {
	define('USER_MODEL', 'User');
}

// Table prefix
if (!defined('ADMIN_PREFIX')) {
	define('ADMIN_PREFIX', 'admin_');
}

// Database config
if (!defined('ADMIN_DATABASE')) {
	define('ADMIN_DATABASE', Configure::read('Acl.database'));
}

/**
 * Current version.
 */
Configure::write('Admin.version', file_get_contents(ADMIN_PLUGIN . 'version.md'));

/**
 * Name of the application.
 */
Configure::write('Admin.appName', __d('admin', 'Admin'));

/**
 * Pseudo plugin name to wrap application models in.
 */
Configure::write('Admin.coreName', 'Core');

/**
 * Aliases for special AROs.
 */
Configure::write('Admin.aliases', array(
	'administrator' => 'Administrator',
	'superModerator' => 'SuperModerator'
));

/**
 * Ignore/restrict these models.
 */
Configure::write('Admin.ignoreModels', array());

/**
 * Enable logging of administrator actions.
 */
Configure::write('Admin.logActions', true);

/**
 * Default settings for each model.
 */
Configure::write('Admin.modelDefaults', array(
	'imageFields' => array('image'),
	'fileFields' => array('image', 'file'),
	'hideFields' => array('lft', 'rght'),
	'paginate' => array(),
	'associationLimit' => 75,
	'batchDelete' => true,
	'actionButtons' => true,
	'deletable' => true,
	'editable' => true,
	'iconClass' => ''
));

/**
 * Behavior methods to execute as process callbacks.
 * The titles are passed through localization and will also replace %s with the model name.
 */
Configure::write('Admin.behaviorCallbacks', array(
	'Tree' => array(
		'recover' => 'Recover Tree',
		'reorder' => 'Reorder Tree'
	),
	'Cacheable' => array(
		'clearCache' => 'Clear Cache'
	)
));

/**
 * Model methods to execute as process callbacks.
 * The callback method accepts a record ID as the 1st argument.
 * The titles are passed through localization and will also replace %s with the model name.
 */
Configure::write('Admin.modelCallbacks', array());

/**
 * Provide overrides for CRUD actions.
 * This allows one to hook into the system and provide their own controller action logic.
 */
Configure::write('Admin.actionOverrides', array());

/**
 * Provide overrides for CRUD views.
 * This allows one to hook into the system and provide their own view template logic.
 */
Configure::write('Admin.viewOverrides', array());

/**
 * The user model for the application.
 */
Configure::write('User.model', USER_MODEL);

/**
 * A map of user fields that are used within this plugin. If your users table has a different naming scheme
 * for the username, email, status, etc fields, you can define their replacement here.
 */
if (!Configure::check('User.fieldMap')) {
	Configure::write('User.fieldMap', array(
		'username'	=> 'username',
		'password'	=> 'password',
		'email'		=> 'email',
		'status'	=> 'status',
		'avatar'	=> 'avatar',
		'locale'	=> 'locale',
		'timezone'	=> 'timezone',
		'lastLogin'	=> 'lastLogin'
	));
}

/**
 * A map of status values for the users "status" column.
 * This column determines if the user is pending, currently active, or banned.
 */
if (!Configure::check('User.statusMap')) {
	Configure::write('User.statusMap', array(
		'pending'	=> 0,
		'active'	=> 1,
		'banned'	=> 2
	));
}

/**
 * A map of external user management URLs.
 */
if (!Configure::check('User.routes')) {
	Configure::write('User.routes', array(
		'login' => array('plugin' => false, 'admin' => false, 'controller' => 'users', 'action' => 'login'),
		'logout' => array('plugin' => false, 'admin' => false, 'controller' => 'users', 'action' => 'logout'),
		'signup' => array('plugin' => false, 'admin' => false, 'controller' => 'users', 'action' => 'signup'),
		'forgotPass' => array('plugin' => false, 'admin' => false, 'controller' => 'users', 'action' => 'forgot_password'),
		'settings' => array('plugin' => false, 'admin' => false, 'controller' => 'users', 'action' => 'settings'),
		'profile' => array('plugin' => false, 'admin' => false, 'controller' => 'users', 'action' => 'profile', '{id}') // {slug}, {username}
	));
}