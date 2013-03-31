<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
 */

class AdminSchema extends CakeSchema {

	public $name = 'Admin';
	public $plugin = 'Admin';
	public $connection = ADMIN_DATABASE;

	/**
	 * Set the table prefix.
	 *
	 * @param array $event
	 * @return bool
	 */
	public function before($event = array()) {
		// @todo doesnt work
		//$db = ConnectionManager::getDataSource($this->connection);
		//$db->config['prefix'] = ADMIN_PREFIX;

		return true;
	}

	/**
	 * Action logs table.
	 *
	 * @var array
	 */
	public $action_logs = array(
		'id' => array('type' => 'integer', 'null' => false, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'action' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 6),
		'model' => array('type' => 'string', 'null' => false, 'length' => 100),
		'foreign_key' => array('type' => 'string', 'null' => false, 'length' => 36),
		'item' => array('type' => 'string', 'null' => true),
		'comment' => array('type' => 'string', 'null' => true),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'user_id' => array('column' => 'user_id', 'unique' => 0)
		)
	);

	/**
	 * Item reports table.
	 *
	 * @var array
	 */
	public $item_reports = array(
		'id' => array('type' => 'integer', 'null' => false, 'key' => 'primary'),
		'reporter_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'resolver_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'key' => 'index'),
		'status' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 6),
		'type' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 6),
		'model' => array('type' => 'string', 'null' => false, 'length' => 100),
		'foreign_key' => array('type' => 'string', 'null' => false, 'length' => 36),
		'item' => array('type' => 'string', 'null' => true),
		'reason' => array('type' => 'text', 'null' => true),
		'comment' => array('type' => 'text', 'null' => true),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'reporter_id' => array('column' => 'reporter_id', 'unique' => 0),
			'resolver_id' => array('column' => 'reporter_id', 'unique' => 0)
		)
	);

}
