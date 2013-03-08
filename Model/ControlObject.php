<?php

App::uses('Aco', 'Model');

class ControlObject extends Aco {

	/**
	 * Overwrite Aco name.
	 *
	 * @var string
	 */
	public $name = 'ControlObject';

	/**
	 * Use alias as display.
	 *
	 * @var string
	 */
	public $displayField = 'alias';

	/**
	 * Use acos table.
	 *
	 * @var string
	 */
	public $useTable = 'acos';

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
			'className' => 'Admin.ControlObject'
		)
	);

	/**
	 * Has many.
	 *
	 * @var array
	 */
	public $hasMany = array(
		'Children' => array(
			'className' => 'Admin.ControlObject',
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
		'RequestObject' => array(
			'className' => 'Admin.RequestObject',
			'with' => 'Admin.ObjectPermission',
			'joinTable' => 'aros_acos',
			'showInForm' => false
		)
	);

	/**
	 * Add an object if it does not exist.
	 *
	 * @param string $alias
	 * @param int $parent_id
	 * @return int
	 */
	public function addObject($alias, $parent_id = null) {
		$query = array(
			'alias' => $alias,
			'parent_id' => $parent_id
		);

		$result = $this->find('first', array(
			'conditions' => $query
		));

		if ($result) {
			return $result['ControlObject']['id'];
		}

		$this->create();

		if ($this->save($query)) {
			return $this->id;
		}

		return null;
	}

	/**
	 * Return all records.
	 *
	 * @return array
	 */
	public function getObjects() {
		$this->recursive = 0;

		return $this->find('all', array(
			'order' => array('ControlObject.alias' => 'ASC'),
			'cache' => __METHOD__,
			'cacheExpires' => '+1 hour'
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
			'conditions' => array('ControlObject.alias' => $alias),
			'cache' => array(__METHOD__, $alias),
			'cacheExpires' => '+24 hours'
		));
	}

}