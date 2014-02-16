<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/admin/blob/master/license.md
 * @link        http://milesj.me/code/cakephp/admin
 */

App::uses('Permission', 'Model');

class ObjectPermission extends Permission {

    const ALLOW = 1;
    const DENY = -1;
    const INHERIT = 0;

    /**
     * Overwrite Permission name.
     *
     * @type string
     */
    public $name = 'ObjectPermission';

    /**
     * Disable recursion.
     *
     * @type int
     */
    public $recursive = -1;

    /**
     * Force to admin plugin.
     *
     * @type string
     */
    public $plugin = 'Admin';

    /**
     * Belongs to.
     *
     * @type array
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
     * @type array
     */
    public $actsAs = array(
        'Containable',
        'Utility.Enumerable'
    );

    /**
     * Enumerable fields.
     *
     * @type array
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
     * @type array
     */
    public $admin = array(
        'iconClass' => 'fa-lock'
    );

    /**
     * Return all records.
     *
     * @return array
     */
    public function getAll() {
        return $this->find('all', array(
            //'cache' => __METHOD__,
            //'cacheExpires' => '+1 hour'
        ));
    }

    /**
     * Return all permissions by ARO ID (accepts multiple IDs).
     *
     * @param int|array $aro_id
     * @return array
     */
    public function getByAroId($aro_id) {
        return $this->find('all', array(
            'conditions' => array('ObjectPermission.aro_id' => $aro_id),
            'order' => array('ControlObject.lft' => 'DESC', 'ObjectPermission.id' => 'ASC'),
            'contain' => array('ControlObject')
        ));
    }

}