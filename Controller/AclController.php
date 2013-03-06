<?php

/**
 * http://book.cakephp.org/2.0/en/core-libraries/components/access-control-lists.html
 *
 * @property Aco $Aco
 * @property Aro $Aro
 * @property Permission $Permission
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
	 * Introspect ACL models and make them available.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->Aco = Admin::introspectModel('Aco');

		$this->Aro = Admin::introspectModel('Aro');
		$this->Aro->recursive = 0; // Use recursive as containable pulls in too much data

		$this->Permission = Admin::introspectModel('Permission');
		$this->Permission->cacheQueries = false;
	}

	/**
	 * Return all the ACOs.
	 *
	 * @return array
	 */
	protected function getControllers() {
		return $this->Aco->find('all', array(
			'order' => array('Aco.alias' => 'ASC'),
			'cache' => array('Aco::getAll'),
			'cacheExpires' => '+1 hour'
		));
	}

	/**
	 * Return all the AROs with their permissions mapped.
	 *
	 * @return array
	 */
	protected function getRequesters() {
		$mapByAroId = array();

		// Map out the permissions
		$permissions = $this->Permission->find('all', array(
			'cache' => array('Permission::getAll'),
			'cacheExpires' => '+1 hour'
		));

		if ($permissions) {
			foreach ($permissions as $permission) {
				$permission = $permission['Permission'];
				$mapByAroId[$permission['aro_id']][$permission['aco_id']] = $permission;
			}
		}

		// Grab the permissions for each aro
		$this->Aro->bindModel(array(
			'belongsTo' => array(
				'Parent' => array(
					'className' => 'Aro',
					'fields' => array('Parent.alias')
				)
			)
		));

		$aros = $this->Aro->find('all', array(
			'cache' => array('Aro::getAll'),
			'cacheExpires' => '+1 hour'
		));

		if ($aros) {
			foreach ($aros as &$aro) {
				$aro['Permission'] = array();
				$id = $aro['Aro']['id'];
				$parent_id = $aro['Aro']['parent_id'];

				// If inheriting from parent, force all values to 0 (inherit)
				if ($parent_id && isset($mapByAroId[$parent_id])) {
					$aro['Permission'] = array_map(function($value) {
						return array_merge($value, array(
							'_create' => 0,
							'_read' => 0,
							'_update' => 0,
							'_delete' => 0
						));
					}, $mapByAroId[$parent_id]) + $aro['Permission'];
				}

				// Individual perms should take precedence
				if (isset($mapByAroId[$id])) {
					$aro['Permission'] = $mapByAroId[$id] + $aro['Permission'];
				}
			}
		}

		return $aros;
	}

}