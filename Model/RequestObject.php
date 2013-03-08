<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
 */

App::uses('Aro', 'Model');

class RequestObject extends Aro {

	/**
	 * Overwrite Aro name.
	 *
	 * @var string
	 */
	public $name = 'RequestObject';

	/**
	 * Use alias as display.
	 *
	 * @var string
	 */
	public $displayField = 'alias';

	/**
	 * Use aros table.
	 *
	 * @var string
	 */
	public $useTable = 'aros';

	/**
	 * Disable recursion.
	 *
	 * @var int
	 */
	public $recursive = -1;

	/**
	 * Admin settings.
	 *
	 * @var array
	 */
	public $admin = array(
		'hideFields' => array('lft', 'rght')
	);

	/**
	 * Belongs to.
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Parent' => array(
			'className' => 'Admin.RequestObject'
		)
	);

	/**
	 * Has many.
	 *
	 * @var array
	 */
	public $hasMany = array(
		'Children' => array(
			'className' => 'Admin.RequestObject',
			'foreignKey' => 'parent_id',
			'dependent' => true,
			'exclusive' => true
		)
	);

	/**
	 * Has and belongs to many.
	 *
	 * @var array
	 */
	public $hasAndBelongsToMany = array(
		'ControlObject' => array(
			'className' => 'Admin.ControlObject',
			'with' => 'Admin.ObjectPermission',
			'joinTable' => 'aros_acos',
			'showInForm' => false
		)
	);

	/**
	 * Add an alias if it does not exist.
	 *
	 * @param string $alias
	 * @return bool
	 */
	public function addAlias($alias) {
		if ($this->hasAlias($alias)) {
			return true;
		}

		$this->create();

		return $this->save(array('alias' => $alias));
	}

	/**
	 * Return all records.
	 *
	 * @return array
	 */
	public function getObjects() {
		$this->recursive = 0;

		return $this->find('all', array(
			'order' => array('RequestObject.lft' => 'ASC'),
			'cache' => __METHOD__,
			'cacheExpires' => '+1 hour'
		));
	}

	/**
	 * Return a record by alias.
	 *
	 * @param string $alias
	 * @return array
	 */
	public function getAlias($alias) {
		return $this->find('first', array(
			'conditions' => array('RequestObject.alias' => $alias),
			'contain' => array('Parent')
		));
	}

	/**
	 * Check if an alias already exists.
	 *
	 * @param string $alias
	 * @return bool
	 */
	public function hasAlias($alias) {
		return (bool) $this->find('count', array(
			'conditions' => array('RequestObject.alias' => $alias),
			'cache' => array(__METHOD__, $alias),
			'cacheExpires' => '+24 hours'
		));
	}

	/**
	 * Check to see if a user is part of the admin ARO.
	 *
	 * @param int $user_id
	 * @return bool
	 */
	public function isAdmin($user_id) {
		$admin = $this->getAlias(Configure::read('Admin.adminAlias'));

		if (!$admin) {
			return false;
		}

		return (bool) $this->find('count', array(
			'conditions' => array(
				'RequestObject.model' => 'User',
				'RequestObject.foreign_key' => $user_id,
				'RequestObject.parent_id' => $admin['RequestObject']['id']
			)
		));
	}

}