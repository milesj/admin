<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
 */

App::uses('AdminAppModel', 'Admin.Model');

class ActionLog extends AdminAppModel {

	const NA = 0;

	// CRUD
	const CREATE = 10;
	const READ = 11;
	const UPDATE = 12;
	const DELETE = 13;
	const BATCH_DELETE = 14;

	// ACL
	const ACL_SYNC = 20;
	const ACL_GRANT = 21;

	/**
	 * Belongs to.
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'User' => array(
			'className' => USER_MODEL
		)
	);

	/**
	 * Enum mapping.
	 *
	 * @var array
	 */
	public $enum = array(
		'action' => array(
			self::NA => 'NA',
			self::CREATE => 'CREATE',
			self::READ => 'READ',
			self::UPDATE => 'UPDATE',
			self::DELETE => 'DELETE',
			self::BATCH_DELETE => 'BATCH_DELETE',
			self::ACL_SYNC => 'ACL_SYNC',
			self::ACL_GRANT => 'ACL_GRANT'
		)
	);

	/**
	 * Admin settings.
	 *
	 * @var array
	 */
	public $admin = array(
		'iconClass' => 'icon-exchange',
		'paginate' => array(
			'order' => array('ActionLog.id' => 'DESC')
		)
	);

}