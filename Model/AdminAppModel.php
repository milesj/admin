<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
 */

class AdminAppModel extends Model {

	/**
	 * Table prefix.
	 *
	 * @var string
	 */
	public $tablePrefix = ADMIN_PREFIX;

	/**
	 * Database config.
	 *
	 * @var string
	 */
	public $useDbConfig = ADMIN_DATABASE;

	/**
	 * Cache queries.
	 *
	 * @var boolean
	 */
	public $cacheQueries = true;

	/**
	 * No recursion.
	 *
	 * @var int
	 */
	public $recursive = -1;

	/**
	 * Behaviors.
	 *
	 * @var array
	 */
	public $actsAs = array(
		'Containable',
		'Utility.Enumerable',
		'Utility.Cacheable' => array(
			'expires' => '+1 hour'
		)
	);

}
