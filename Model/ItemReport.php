<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
 */

App::uses('AdminAppModel', 'Admin.Model');

class ItemReport extends AdminAppModel {

	// Status
	const PENDING = 0;
	const RESOLVED = 1;
	const INVALID = 2;

	// Type
	const OTHER = 0;
	const VIOLENCE = 1; // physical fighting / abuse
	const OFFENSIVE = 2; // animal / child abuse
	const HATEFUL = 3; // hate crimes, bullying
	const HARMFUL = 4; // dangerous acts, self injury, etc
	const SPAM = 5; // spam, fraud, misleading ads
	const COPYRIGHT = 6; // infringement, etc
	const SEXUAL = 7; // nudity, sex, etc
	const HARASSMENT = 8; // user to user, threats, trolling

	/**
	 * Display field.
	 *
	 * @var string
	 */
	public $displayField = 'item';

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
		),
		'type' => array(
			self::OTHER => 'OTHER',
			self::VIOLENCE => 'VIOLENCE',
			self::OFFENSIVE => 'OFFENSIVE',
			self::HATEFUL => 'HATEFUL',
			self::HARMFUL => 'HARMFUL',
			self::SPAM => 'SPAM',
			self::COPYRIGHT => 'COPYRIGHT',
			self::SEXUAL => 'SEXUAL',
			self::HARASSMENT => 'HARASSMENT'
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

	/**
	 * Remove core plugin from models.
	 *
	 * @param array $options
	 * @return bool
	 */
	public function beforeSave($options = array()) {
		if (isset($this->data[$this->alias]['model'])) {
			list($plugin, $model) = Admin::parseName($this->data[$this->alias]['model']);

			if ($plugin === Configure::read('Admin.coreName')) {
				$this->data[$this->alias]['model'] = $model;
			}
		}

		return true;
	}

}