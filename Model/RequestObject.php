<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/admin/blob/master/license.md
 * @link        http://milesj.me/code/cakephp/admin
 */

App::uses('Aro', 'Model');

class RequestObject extends Aro {

    /**
     * Overwrite Aro name.
     *
     * @type string
     */
    public $name = 'RequestObject';

    /**
     * Use alias as display.
     *
     * @type string
     */
    public $displayField = 'alias';

    /**
     * Use aros table.
     *
     * @type string
     */
    public $useTable = 'aros';

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
     * Admin settings.
     *
     * @type array
     */
    public $admin = array(
        'iconClass' => 'fa-key',
        'hideFields' => array('lft', 'rght'),
        'paginate' => array(
            'order' => array('RequestObject.lft' => 'ASC')
        )
    );

    /**
     * Belongs to.
     *
     * @type array
     */
    public $belongsTo = array(
        'Parent' => array(
            'className' => 'Admin.RequestObject',
            'foreignKey' => 'parent_id'
        ),
        'User' => array(
            'className' => USER_MODEL,
            'foreignKey' => 'foreign_key',
            //'conditions' => array('RequestObject.model' => USER_MODEL)
        )
    );

    /**
     * Has many.
     *
     * @type array
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
     * @type array
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
     * Behaviors.
     *
     * @type array
     */
    public $actsAs = array(
        'Tree' => array('type' => 'nested'),
        'Containable',
        'Utility.Cacheable'
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
            return $result;
        }

        $this->create();

        if ($this->save($query)) {
            $this->deleteCache(array('RequestObject::hasAlias', $alias));

            return $this->getByAlias($alias);
        }

        return null;
    }

    /**
     * Add a child object if it does not exist.
     *
     * @param string $alias
     * @param int $parent_id
     * @param string $model
     * @param int $foreignKey
     * @return int
     */
    public function addChildObject($alias, $parent_id, $model, $foreignKey) {
        $query = array(
            'alias' => $alias,
            'parent_id' => $parent_id,
            'model' => $model,
            'foreign_key' => $foreignKey
        );

        $result = $this->find('first', array(
            'conditions' => $query
        ));

        if ($result) {
            return $result;
        }

        $this->create();

        if ($this->save($query)) {
            $this->deleteCache(array('RequestObject::hasAlias', $alias));

            return $this->getByAlias($alias);
        }

        return null;
    }

    /**
     * Return all records.
     *
     * @return array
     */
    public function getAll() {
        $this->recursive = -1;

        return $this->find('all', array(
            'order' => array('RequestObject.lft' => 'ASC'),
            'contain' => array('Parent'),
            //'cache' => __METHOD__,
            //'cacheExpires' => '+1 hour'
        ));
    }

    /**
     * Return all records.
     *
     * @return array
     */
    public function getParents() {
        return $this->find('all', array(
            'conditions' => array('RequestObject.parent_id' => null),
            'cache' => __METHOD__
        ));
    }

    /**
     * Return all records as a list.
     *
     * @return array
     */
    public function getList() {
        return $this->find('list', array(
            'conditions' => array('RequestObject.parent_id' => null),
            'fields' => array('RequestObject.id', 'RequestObject.alias'),
            'cache' => __METHOD__
        ));
    }

    /**
     * Return a record based on ID.
     *
     * @param int $id
     * @return array
     */
    public function getById($id) {
        return $this->find('first', array(
            'conditions' => array('RequestObject.id' => $id),
            'contain' => array('Parent', 'User'),
            'cache' => array(__METHOD__, $id)
        ));
    }

    /**
     * Return a record based on user ID.
     *
     * @param int $user_id
     * @return array
     */
    public function getByUserId($user_id) {
        return $this->find('first', array(
            'conditions' => array(
                'RequestObject.foreign_key' => $user_id,
                'RequestObject.model' => USER_MODEL
            ),
            'contain' => array('Parent', 'User'),
            'cache' => array(__METHOD__, $user_id)
        ));
    }

    /**
     * Return a record based on alias.
     *
     * @param string $alias
     * @return array
     */
    public function getByAlias($alias) {
        return $this->find('first', array(
            'conditions' => array('RequestObject.alias' => $alias),
            'contain' => array('Parent'),
            'cache' => array(__METHOD__, $alias)
        ));
    }

    /**
     * Return a list of users permissions. Include parent groups permissions also.
     *
     * @param int $user_id
     * @return array
     */
    public function getPermissions($user_id) {
        try {
            $aros = $this->node(array(USER_MODEL => array('id' => $user_id)));
        } catch (Exception $e) {
            return array();
        }

        return $this->ObjectPermission->getByAroId(Hash::extract($aros, '{n}.RequestObject.id'));
    }

    /**
     * Map all permissions to a boolean flagged list grouped by ACO and CRUD.
     *
     * @param int $user_id
     * @param string $filter
     * @return array
     */
    public function getCrudPermissions($user_id, $filter = null) {
        return $this->cache(array(__METHOD__, $user_id), function($self) use ($user_id, $filter) {
            $crud = array();

            if ($permissions = $self->getPermissions($user_id)) {
                foreach ($permissions as $permission) {
                    if ($filter && stripos($permission['ControlObject']['alias'], $filter) === false) {
                        continue;
                    }

                    $crud[$permission['ControlObject']['alias']] = array(
                        'create' => ($permission['ObjectPermission']['_create'] >= 0),
                        'read' => ($permission['ObjectPermission']['_read'] >= 0),
                        'update' => ($permission['ObjectPermission']['_update'] >= 0),
                        'delete' => ($permission['ObjectPermission']['_delete'] >= 0)
                    );
                }
            }

            return $crud;
        });
    }

    /**
     * Return a list of users roles defined in the ACL.
     *
     * @param int $user_id
     * @return array
     */
    public function getRoles($user_id) {
        $results = $this->find('all', array(
            'conditions' => array(
                'RequestObject.foreign_key' => $user_id,
                'RequestObject.model' => USER_MODEL
            )
        ));

        if ($results) {
            // Grab the direct parents (or roles)
            $results = $this->find('all', array(
                'conditions' => array('RequestObject.id' => Hash::extract($results, '{n}.RequestObject.parent_id'))
            ));

            // Also grab the children of the parents since the user has access to them as well
            $results = array_merge($results, $this->find('all', array(
                'conditions' => array(
                    'RequestObject.parent_id' => Hash::extract($results, '{n}.RequestObject.id'),
                    'RequestObject.model' => null,
                    'RequestObject.foreign_key' => null
                )
            )));
        }

        return $results;
    }

    /**
     * Return all the staff.
     *
     * @return array
     */
    public function getStaff() {
        return $this->find('all', array(
            'conditions' => array(
                'RequestObject.parent_id' => array_keys($this->getList()),
                'RequestObject.foreign_key !=' => null,
                'RequestObject.model' => USER_MODEL
            ),
            'contain' => array('User', 'Parent')
        ));
    }

    /**
     * Return all the staff by slug.
     *
     * @param string $alias
     * @return array
     */
    public function getStaffByAlias($alias) {
        $access = $this->getByAlias($alias);

        return $this->find('all', array(
            'conditions' => array(
                'RequestObject.parent_id' => $access['RequestObject']['id'],
                'RequestObject.foreign_key !=' => null,
                'RequestObject.model' => USER_MODEL
            ),
            'contain' => array('User', 'Parent'),
            'cache' => array(__METHOD__, $alias)
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
     * Check to see if a user is a child of a specific alias.
     *
     * @param int $user_id
     * @param int|string $fk
     * @return bool
     */
    public function isChildOf($user_id, $fk) {
        if (!$user_id || !$fk) {
            return false;
        }

        try {
            $aros = $this->node(array(
                'model' => USER_MODEL,
                'foreign_key' => $user_id
            ));
        } catch (Exception $e) {
            return false;
        }

        if ($aros) {
            foreach ($aros as $aro) {
                if (is_string($fk) && $aro['RequestObject']['alias'] == $fk) {
                    return true;
                } else if (is_numeric($fk) && $aro['RequestObject']['id'] == $fk) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check to see if a user is an admin.
     *
     * @param int $user_id
     * @return bool
     */
    public function isAdmin($user_id) {
        return $this->isChildOf($user_id, Configure::read('Admin.aliases.administrator'));
    }

    /**
     * Check to see if a user is a super moderator.
     *
     * @param int $user_id
     * @return bool
     */
    public function isSuperMod($user_id) {
        return $this->isChildOf($user_id, Configure::read('Admin.aliases.superModerator'));
    }

}