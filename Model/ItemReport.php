<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
 */

App::uses('AdminAppModel', 'Admin.Model');

class ItemReport extends AdminAppModel {

	const PENDING = 0;
	const RESOLVED = 1;
	const INVALID = 2;

	/**
	 * Belongs to.
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Reporter' => array(
			'className' => USER_MODEL,
			'foreignKey' => 'reporter_id'
		),
		'Resolver' => array(
			'className' => USER_MODEL,
			'foreignKey' => 'resolver_id'
		)
	);

	/**
	 * Admin settings.
	 *
	 * @var array
	 */
	public $admin = array(
		'iconClass' => 'icon-flag',
		'editable' => false,
		'deletable' => false
	);

	/**
	 * Enum mapping.
	 *
	 * @var array
	 */
	public $enum = array(
		'status' => array(
			self::PENDING => 'PENDING',
			self::RESOLVED => 'RESOLVED',
			self::INVALID => 'INVALID'
		)
	);

	/**
	 * Count record by status.
	 *
	 * @param int $status
	 * @return array
	 */
	public function getCountByStatus($status = self::PENDING) {
		return $this->find('count', array(
			'conditions' => array('ItemReport.status' => $status),
			'cache' => array(__METHOD__, $status)
		));
	}

}