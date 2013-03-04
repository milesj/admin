<?php
$belongsTo = null;

foreach ($data['belongsTo'] as $bt) {
	if (!empty($result[$bt['alias']]['id'])) {
		$belongsTo = $bt;
		break;
	}
}

if (!$belongsTo) { ?>

	<span class="belongs-to text-error">INVALID ASSOC</span>

<?php } else {
	$foreignModel = $belongsTo['alias'];
	$displayField = $result[$foreignModel][$model->{$foreignModel}->displayField];

	echo $this->Html->link($displayField, array(
		'plugin' => 'admin',
		'controller' => 'crud',
		'action' => 'read',
		'model' => Inflector::underscore($belongsTo['model']),
		$value
	), array(
		'class' => 'belongs-to'
	));
}