<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
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
			throw new BadRequestException(__('Invalid ARO/ACO IDs'));
		}

		$aro = $this->Aro->getById($aro_id);
		$aco = $this->Aco->getById($aco_id);

		if (!$aro || !$aco) {
			throw new NotFoundException(__('Invalid ARO/ACO Records'));
		}

		$aroAlias = $aro['RequestObject']['alias'];
		$acoAlias = $aco['ControlObject']['alias'];

		if (!empty($aro['Parent']['alias'])) {
			$aroAlias = $aro['Parent']['alias'] . '/' . $aroAlias;
		}

		if ($this->Acl->allow($aroAlias, $acoAlias)) {
			$this->AdminToolbar->logAction(ActionLog::ACL_GRANT, null, null, sprintf('Granted %s access to %s', $aroAlias, $acoAlias));
			$this->AdminToolbar->setFlashMessage(__('Successfully granted %s permission to %s', array($aroAlias, $acoAlias)));
		} else {
			$this->AdminToolbar->setFlashMessage(__('Failed to grant %s permission to %s', array($aroAlias, $acoAlias)), 'error');
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
		$this->Aro->recursive = 0; // Use recursive as containable pulls in too much data

		$this->Permission = Admin::introspectModel('Admin.ObjectPermission');
		$this->Permission->cacheQueries = false;
	}

	/**
	 * Return all the ACOs.
	 *
	 * @return array
	 */
	protected function getControllers() {
		return $this->Aco->getAll();
	}

	/**
	 * Return all the AROs with their permissions mapped.
	 *
	 * @return array
	 */
	protected function getRequesters() {
		$mapByAroId = array();

		// Map out the permissions
		if ($permissions = $this->Permission->getAll()) {
			foreach ($permissions as $permission) {
				$permission = $permission['ObjectPermission'];
				$mapByAroId[$permission['aro_id']][$permission['aco_id']] = $permission;
			}
		}

		// Grab the permissions for each aro
		$aros = $this->Aro->getAll();

		if ($aros) {
			foreach ($aros as &$aro) {
				$aro['ObjectPermission'] = array();
				$id = $aro['RequestObject']['id'];
				$parent_id = $aro['RequestObject']['parent_id'];

				// If inheriting from parent, force all values to 0 (inherit)
				if ($parent_id && isset($mapByAroId[$parent_id])) {
					$aro['ObjectPermission'] = array_map(function($value) {
						return array_merge($value, array(
							'_create' => 0,
							'_read' => 0,
							'_update' => 0,
							'_delete' => 0
						));
					}, $mapByAroId[$parent_id]) + $aro['ObjectPermission'];
				}

				// Individual perms should take precedence
				if (isset($mapByAroId[$id])) {
					$aro['ObjectPermission'] = $mapByAroId[$id] + $aro['ObjectPermission'];
				}
			}
		}

		return $aros;
	}

}