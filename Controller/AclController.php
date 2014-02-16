<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/admin/blob/master/license.md
 * @link        http://milesj.me/code/cakephp/admin
 */

/**
 * Manage ACL: http://book.cakephp.org/2.0/en/core-libraries/components/access-control-lists.html
 *
 * @property ControlObject $Aco
 * @property RequestObject $Aro
 * @property ObjectPermission $Permission
 */
class AclController extends AdminAppController {

    /**
     * Create a matrix table of ACOs, AROs and their permissions.
     */
    public function index() {
        $this->set('aros', $this->getRequesters());
        $this->set('acos', $this->getControllers());
    }

    /**
     * Grant an ARO access to an ACO.
     *
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function grant() {
        $aro_id = isset($this->request->params['named']['aro_id']) ? $this->request->params['named']['aro_id'] : null;
        $aco_id = isset($this->request->params['named']['aco_id']) ? $this->request->params['named']['aco_id'] : null;

        if (!$aro_id || !$aco_id) {
            throw new BadRequestException(__d('admin', 'Invalid ARO/ACO IDs'));
        }

        $aro = $this->Aro->getById($aro_id);
        $aco = $this->Aco->getById($aco_id);

        if (!$aro || !$aco) {
            throw new NotFoundException(__d('admin', 'Invalid ARO/ACO Records'));
        }

        $aroAlias = $aro['RequestObject']['alias'];
        $acoAlias = $aco['ControlObject']['alias'];

        if (!empty($aro['Parent']['alias'])) {
            $aroAlias = $aro['Parent']['alias'] . '/' . $aroAlias;
        }

        if ($this->Acl->allow($aroAlias, $acoAlias)) {
            $this->AdminToolbar->logAction(ActionLog::CREATE, $this->Permission, $this->Acl->adapter()->Permission->id, sprintf('Granted %s access to %s', $aroAlias, $acoAlias));
            $this->AdminToolbar->setFlashMessage(__d('admin', 'Successfully granted %s permission to %s', array($aroAlias, $acoAlias)));
        } else {
            $this->AdminToolbar->setFlashMessage(__d('admin', 'Failed to grant %s permission to %s', array($aroAlias, $acoAlias)), 'is-error');
        }

        $this->redirect(array('action' => 'index'));
    }

    /**
     * Introspect ACL models and make them available.
     */
    public function beforeFilter() {
        parent::beforeFilter();

        $this->Aco = Admin::introspectModel('Admin.ControlObject');
        $this->Aro = Admin::introspectModel('Admin.RequestObject');
        $this->Permission = Admin::introspectModel('Admin.ObjectPermission');
        $this->Permission->cacheQueries = false;
    }

    /**
     * Return all the ACOs.
     *
     * @return array
     */
    protected function getControllers() {
        $mapParentId = array();

        $acos = $this->Aco->getAll();

        // Map IDs to parent IDs
        foreach ($acos as $aco) {
            $mapParentId[$aco['ControlObject']['id']] = $aco['ControlObject']['parent_id'];
        }

        // Determine the child depth
        foreach ($acos as &$aco) {
            $depth = 0;
            $id = $aco['ControlObject']['id'];

            while (isset($mapParentId[$id])) {
                $depth++;
                $id = $mapParentId[$id];
            }

            $aco['ControlObject']['depth'] = $depth;
        }

        return $acos;
    }

    /**
     * Return all the AROs with their permissions mapped.
     *
     * @return array
     */
    protected function getRequesters() {
        $mapParentId = array();
        $mapAroId = array();

        $aros = $this->Aro->getAll();
        $permissions = $this->Permission->getAll();

        // Map ACOs to AROs indexed by IDs
        foreach ($permissions as $permission) {
            $permission = $permission['ObjectPermission'];

            $mapAroId[$permission['aro_id']][$permission['aco_id']] = $permission;
        }

        // Map IDs to parent IDs
        foreach ($aros as $aro) {
            $mapParentId[$aro['RequestObject']['id']] = $aro['RequestObject'];
        }

        // Loop through AROs and determine permissions
        // While taking into account inheritance
        foreach ($aros as &$aro) {
            $id = $aro['RequestObject']['id'];
            $parent_id = $aro['RequestObject']['parent_id'];
            $inheritance = array();

            while (isset($mapParentId[$parent_id])) {
                array_unshift($inheritance, $parent_id);
                $parent_id = $mapParentId[$parent_id]['parent_id'];
            }

            $inheritance[] = $id;

            // Fetch permissions from parents
            $perms = array();
            $parent_id = $aro['RequestObject']['parent_id']; // reset $parent_id

            foreach ($inheritance as $pid) {
                if (isset($mapAroId[$pid])) {
                    $perms = Hash::merge($perms, array_map(function($value) use ($id, $parent_id) {

                        // If the ARO on the permission doesn't match the current ARO
                        // It is being inherited, so force it to 0
                        if ($id != $value['aro_id']) {
                            $value = array_merge($value, array(
                                '_create' => 0,
                                '_read' => 0,
                                '_update' => 0,
                                '_delete' => 0
                            ));

                        // Top level AROs cant inherit from nothing
                        // So change those values to denied
                        } else if (empty($parent_id)) {
                            foreach (array('_create', '_read', '_update', '_delete') as $action) {
                                if ($value[$action] == 0) {
                                    $value[$action] = -1;
                                }
                            }
                        }

                        return $value;
                    }, $mapAroId[$pid]));
                }
            }

            $aro['ObjectPermission'] = $perms;
        }

        return $aros;
    }

}