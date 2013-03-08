<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
 */

App::uses('Permission', 'Model');

class ObjectPermission extends Permission {

	const ALLOW = 1;
	const DENY = -1;
	const INHERIT = 0;

	/**
	 * Overwrite Permission name.
	 *
	 * @var string
	 */
	public $name = 'ObjectPermission';

	/**
	 * Disable recursion.
	 *
	 * @var int
	 */
	public $recursive = -1;

	/**
	 * Belongs to.
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'RequestObject' => array(
			'className' => 'Admin.RequestObject',
			'foreignKey' => 'aro_id'
		),
		'ControlObject' => array(
			'className' => 'Admin.ControlObject',
			'foreignKey' => 'aco_id'
		)
	);

	/**
	 * Behaviors.
	 *
	 * @var array
	 */
	public $actsAs = array(
		'Utility.Enumerable' => array(
			'format' => 'append'
		)
	);

	/**
	 * Enumerable fields.
	 *
	 * @var array
	 */
	public $enum = array(
		'_create' => array(
			self::ALLOW => 'ALLOW',
			self::DENY => 'DENY',
			self::INHERIT => 'INHERIT'
		),
		'_read' => array(
			self::ALLOW => 'ALLOW',
			self::DENY => 'DENY',
			self::INHERIT => 'INHERIT'
		),
		'_update' => array(
			self::ALLOW => 'ALLOW',
			self::DENY => 'DENY',
			self::INHERIT => 'INHERIT'
		),
		'_delete' => array(
			self::ALLOW => 'ALLOW',
			self::DENY => 'DENY',
			self::INHERIT => 'INHERIT'
		)
	);

	/**
	 * Admin settings.
	 *
	 * @var array
	 */
	public $admin = array(
		'icon' => 'tasks'
	);

	/**
	 * Return all records.
	 *
	 * @return array
	 */
	public function getPermissions() {
		return $this->find('all', array(
			'cache' => __METHOD__,
			'cacheExpires' => '+1 hour'
		));
	}

}