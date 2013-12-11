<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/admin/blob/master/license.md
 * @link        http://milesj.me/code/cakephp/admin
 */

class AdminAppModel extends Model {

    /**
     * Force plugin name.
     *
     * @type string
     */
    public $plugin = 'Admin';

    /**
     * Table prefix.
     *
     * @type string
     */
    public $tablePrefix = ADMIN_PREFIX;

    /**
     * Database config.
     *
     * @type string
     */
    public $useDbConfig = ADMIN_DATABASE;

    /**
     * Cache queries.
     *
     * @type boolean
     */
    public $cacheQueries = true;

    /**
     * No recursion.
     *
     * @type int
     */
    public $recursive = -1;

    /**
     * Enum mapping.
     *
     * @type array
     */
    public $enum = array();

    /**
     * Behaviors.
     *
     * @type array
     */
    public $actsAs = array(
        'Containable',
        'Utility.Enumerable',
        'Utility.Cacheable' => array(
            'expires' => '+1 hour'
        )
    );

}
