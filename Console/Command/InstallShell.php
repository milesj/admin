<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
 */

App::uses('Admin', 'Admin.Lib');
App::uses('BaseInstallShell', 'Utility.Console/Command');

/**
 * @property ControlObject $ControlObject
 * @property RequestObject $RequestObject
 * @property Permission $Permission
 */
class InstallShell extends BaseInstallShell {

	/**
	 * Models.
	 *
	 * @var array
	 */
	public $uses = array('Admin.RequestObject', 'Admin.ControlObject', 'Permission');

	/**
	 * Trigger install.
	 */
	public function main() {
		$this->setSteps(array(
			'Check Database Configuration' => 'checkDbConfig',
			'Set Table Prefix' => 'checkTablePrefix',
			'Set Users Table' => 'checkUsersTable',
			'Check Table Status' => 'checkRequiredTables',
			'Create Database Tables' => 'createTables',
			'Setup ACL' => 'setupAcl',
			'Finish Installation' => 'finish'
		))
		->setDbConfig(ADMIN_DATABASE)
		->setTablePrefix(ADMIN_PREFIX)
		->setRequiredTables(array('users', 'aros', 'acos', 'aros_acos'));

		$this->out('Plugin: Admin v' . Configure::read('Admin.version'));
		$this->out('Copyright: Miles Johnson, 2010-' . date('Y'));
		$this->out('Help: http://milesj.me/code/cakephp/admin');

		parent::main();
	}

	/**
	 * Setup all the ACL records.
	 *
	 * @return bool
	 */
	public function setupAcl() {
		$adminAlias = Configure::read('Admin.adminAlias');

		$this->out(sprintf('Administrator Role: <comment>%s</comment>', $adminAlias));

		$answer = strtoupper($this->in('<question>Is this correct?</question>', array('Y', 'N')));

		if ($answer === 'N') {
			$this->out('<warning>Configure the role through Admin.adminAlias</warning>');
			return false;
		}

		$admin = $this->RequestObject->addObject($adminAlias);
		$userModel = ClassRegistry::init($this->usersModel);

		// Fetch user
		$this->out('<question>What user would you like to give admin access?</question>');
		$user_id = $this->findUser();

		// Give access
		$result = $this->RequestObject->addChildObject(
			$this->user[$userModel->alias][$userModel->displayField],
			$admin['RequestObject']['id'],
			$this->usersModel,
			$user_id);

		if (!$result) {
			$this->err('<error>Failed to give user admin access</error>');
			return false;
		}

		$this->out('<info>Access granted, proceeding...</info>');
		$this->plugin(Configure::read('Admin.coreName'));
		$this->plugin('Admin');

		return true;
	}

	/**
	 * Finalize the installation.
	 *
	 * @return bool
	 */
	public function finish() {
		$this->hr(1);
		$this->out('Admin installation complete!');
		$this->out('Please read the documentation for further instructions:');
		$this->out('http://milesj.me/code/cakephp/admin');
		$this->hr(1);

		return true;
	}

	/**
	 * Install ACOs for all plugin models.
	 *
	 * @param string $name
	 */
	public function plugin($name = null) {
		$this->out();

		$pluginName = $name ?: $this->args[0];
		$plugin = Admin::getPlugin($pluginName);

		if (!$plugin) {
			$this->err(sprintf('<error>%s plugin does not exist</error>', $pluginName));
			return;
		}

		$this->out(sprintf('<info>Installing %s...</info>', $pluginName));

		// Create parent object
		$parent_id = $this->ControlObject->addObject($pluginName);
		$adminAlias = Configure::read('Admin.adminAlias');

		// Create children objects
		foreach ($plugin['models'] as $model) {
			$this->out($model['title']);

			$this->ControlObject->addObject($model['id'], $parent_id);

			// Give admin access
			$this->Permission->allow($adminAlias, $pluginName . '/' . $model['id']);
		}

		$this->out(sprintf('<info>%s model ACOs installed</info>', $pluginName));
	}

	/**
	 * Install ACOs for a single model.
	 *
	 * @param string $name
	 */
	public function model($name = null) {
		$this->out();

		$modelName = Inflector::classify($name ?: $this->args[0]);
		$model = ClassRegistry::init($modelName);

		if (get_class($model) === 'AppModel') {
			$this->err(sprintf('<error>%s model does not exist</error>', $modelName));
			return;
		}

		list($plugin, $modelName) = pluginSplit($modelName);

		if (!$plugin) {
			$plugin = Configure::read('Admin.coreName');
		}

		$this->out(sprintf('<info>Installing %s...</info>', $modelName));

		// Create parent object
		$parent_id = $this->ControlObject->addObject($plugin);

		// Create children object
		$alias = $plugin . '.' . $modelName;
		$this->ControlObject->addObject($alias, $parent_id);

		// Give admin access
		$this->Permission->allow(Configure::read('Admin.adminAlias'), $plugin . '/' . $alias);

		$this->out(sprintf('<info>%s ACOs installed</info>', $modelName));
	}

	/**
	 * Add sub-commands.
	 *
	 * @return ConsoleOptionParser
	 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();

		$parser->addSubcommand('plugin', array(
			'help' => 'Install ACOs for all plugin models',
			'parser' => array(
				'description' => 'This command will install ACO (access control objects) for every plugin model. This will allow for CRUD level access permissions for users.',
				'arguments' => array(
					'plugin' => array('help' => 'Plugin name', 'required' => true)
				)
			)
		));

		$parser->addSubcommand('model', array(
			'help' => 'Install ACOs for a single model',
			'parser' => array(
				'description' => 'This command will install ACO (access control objects) for the model. This will allow for CRUD level access permissions for users.',
				'arguments' => array(
					'model' => array('help' => 'Model name', 'required' => true)
				)
			)
		));

		return $parser;
	}

}