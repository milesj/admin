<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/admin/blob/master/license.md
 * @link        http://milesj.me/code/cakephp/admin
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
     * Belongs to.
     *
     * @type array
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
     * @type array
     */
    public $admin = array(
        'iconClass' => 'fa-flag',
        'editable' => false,
        'deletable' => false
    );

    /**
     * Enum mapping.
     *
     * @type array
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
     * Mark a report as resolved or invalid.
     *
     * @param int $id
     * @param int $status
     * @param int $user_id
     * @param string $comment
     * @return bool
     */
    public function markAs($id, $status, $user_id, $comment = null) {
        $this->id = $id;

        return $this->save(array(
            'status' => $status,
            'resolver_id' => $user_id,
            'comment' => $comment
        ));
    }

    /**
     * Log a unique report only once every 7 days.
     *
     * @param array $query
     * @return bool
     */
    public function reportItem($query) {
        $conditions = $query;
        $conditions['created >='] = date('Y-m-d H:i:s', strtotime('-7 days'));

        unset($conditions['type'], $conditions['item'], $conditions['reason'], $conditions['comment']);

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