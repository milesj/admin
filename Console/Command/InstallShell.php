<?php

App::uses('Admin', 'Admin.Lib');
App::uses('AppShell', 'Console/Command');

class InstallShell extends AppShell {

	/**
	 * Models.
	 *
	 * @var array
	 */
	public $uses = array('Aro', 'Aco');

	/**
	 * Output help text.
	 */
	public function main() {
		$this->out($this->OptionParser->help());
	}

	/**
	 * Install ACOs for all plugin models.
	 */
	public function plugin() {
		$plugins = Admin::getModels();
		$pluginName = $this->args[0];

		if (empty($plugins[$pluginName])) {
			$this->err(sprintf('<error>%s plugin does not exist</error>', $pluginName));
			return;
		}

		$plugin = $plugins[$pluginName];

		foreach ($plugin['models'] as $model) {
			$exists = (bool) $this->Aco->find('count', array(
				'conditions' => array('Aco.alias' => $model['class']),
				'recursive' => -1
			));

			if ($exists) {
				continue;
			}

			$this->Aco->create();
			$this->Aco->save(array('alias' => $model['class']));
		}

		$this->out(sprintf('<info>%s model ACOs installed</info>', $pluginName));
	}

	/**
	 * Install ACOs for a single model.
	 */
	public function model() {
		$modelName = Inflector::classify($this->args[0]);
		$model = ClassRegistry::init($modelName);

		if (get_class($model) === 'AppModel') {
			$this->err(sprintf('<error>%s model does not exist</error>', $modelName));
			return;
		}

		$className = $modelName;

		if (strpos($className, '.') === false) {
			$className = Configure::read('Admin.coreName') . '.' . $className;
		}

		$exists = (bool) $this->Aco->find('count', array(
			'conditions' => array('Aco.alias' => $className),
			'recursive' => -1
		));

		if (!$exists) {
			$this->Aco->create();
			$this->Aco->save(array('alias' => $className));
		}

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