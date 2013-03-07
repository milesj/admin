<?php

App::uses('Admin', 'Admin.Lib');
App::uses('BaseInstallShell', 'Utility.Console/Command');

/**
 * @property ControlObject $ControlObject
 * @property RequestObject $RequestObject
 */
class InstallShell extends BaseInstallShell {

	/**
	 * Models.
	 *
	 * @var array
	 */
	public $uses = array('Admin.RequestObject', 'Admin.ControlObject');

	/**
	 * Trigger install.
	 */
	public function main() {
		$this->setSteps(array(
			'Check Database Configuration' => 'checkDbConfig',
			'Set Table Prefix' => 'checkTablePrefix',
			'Check Table Status' => 'checkRequiredTables',
			'Setup ACL' => 'setupAcl',
			'Finish Installation' => 'finish'
		))
		->setDbConfig('default')
		->setRequiredTables(array('aros', 'acos', 'aros_acos'));

		$this->out('Plugin: Admin v' . Configure::read('Admin.version'));
		$this->out('Copyright: Miles Johnson, 2010-' . date('Y'));
		$this->out('Help: http://milesj.me/code/cakephp/admin');

		parent::main();
	}

	/**
	 * Setup all the ACL records.
	 */
	public function setupAcl() {
		$this->plugin('Admin');
		$this->out('<info>Proceeding...</info>');

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
		//$this->out('Please read the documentation for further instructions:');
		//$this->out('http://milesj.me/code/cakephp/admin');
		$this->hr(1);

		return true;
	}

	/**
	 * Install ACOs for all plugin models.
	 *
	 * @param string $name
	 */
	public function plugin($name = null) {
		$pluginName = $name ?: $this->args[0];
		$plugin = Admin::getPlugin($pluginName);

		if (!$plugin) {
			$this->err(sprintf('<error>%s plugin does not exist</error>', $pluginName));
			return;
		}

		foreach ($plugin['models'] as $model) {
			$this->ControlObject->addAlias($model['id']);
		}

		$this->out(sprintf('<info>%s model ACOs installed</info>', $pluginName));
	}

	/**
	 * Install ACOs for a single model.
	 *
	 * @param string $name
	 */
	public function model($name = null) {
		$modelName = Inflector::classify($name ?: $this->args[0]);
		$model = ClassRegistry::init($modelName);

		if (get_class($model) === 'AppModel') {
			$this->err(sprintf('<error>%s model does not exist</error>', $modelName));
			return;
		}

		$className = $modelName;

		if (strpos($className, '.') === false) {
			$className = Configure::read('Admin.coreName') . '.' . $className;
		}

		$this->ControlObject->addAlias($className);

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