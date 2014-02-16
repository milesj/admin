<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/admin/blob/master/license.md
 * @link        http://milesj.me/code/cakephp/admin
 */

App::uses('AdminAppModel', 'Admin.Model');

class ActionLog extends AdminAppModel {

    const OTHER = 0;

    // CRUD
    const CREATE = 1;
    const READ = 2;
    const UPDATE = 3;
    const DELETE = 4;
    const BATCH_DELETE = 5;

    // Misc
    const PROCESS = 6;
    const BATCH_PROCESS = 7;

    /**
     * Display field.
     *
     * @type string
     */
    public $displayField = 'item';

    /**
     * Belongs to.
     *
     * @type array
     */
    public $belongsTo = array(
        'User' => array(
            'className' => USER_MODEL
        )
    );

    /**
     * Enum mapping.
     *
     * @type array
     */
    public $enum = array(
        'action' => array(
            self::OTHER => 'OTHER',
            self::CREATE => 'CREATE',
            self::READ => 'READ',
            self::UPDATE => 'UPDATE',
            self::DELETE => 'DELETE',
            self::BATCH_DELETE => 'BATCH_DELETE',
            self::PROCESS => 'PROCESS',
            self::BATCH_PROCESS => 'BATCH_PROCESS'
        )
    );

    /**
     * Admin settings.
     *
     * @type array
     */
    public $admin = array(
        'iconClass' => 'fa-exchange',
        'editable' => false,
        'deletable' => false,
        'paginate' => array(
            'order' => array('ActionLog.id' => 'DESC')
        )
    );

    /**
     * Log an action only once every 6 hours.
     *
     * @param array $query
     * @return bool
     */
    public function logAction($query) {
        $interval = Configure::read('Admin.logActions.interval') ?: '-6 hours';

        $conditions = $query;
        $conditions['created >='] = date('Y-m-d H:i:s', strtotime($interval));

        $count = $this->find('count', array(
            'conditions' => $conditions
        ));

        if ($count) {
            return true;
        }

        $this->create();

        return $this->save($query, false);
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