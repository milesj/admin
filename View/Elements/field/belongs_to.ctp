<?php
$rendered = false;

foreach ($data['belongsTo'] as $foreignModel => $className) {
	$belongsTo = $model->{$foreignModel};

	if (!empty($result[$foreignModel][$belongsTo->primaryKey])) {

		echo $this->Html->link($result[$foreignModel][$belongsTo->displayField], array(
			'plugin' => 'admin',
			'controller' => 'crud',
			'action' => 'read',
			'model' => Inflector::underscore($className),
			$value
		), array(
			'class' => 'belongs-to'
		));

		$rendered = true;
		break;
	}
}

if (!$rendered) { ?>

	<span class="belongs-to text-error">INVALID ASSOC</span>

<?php }