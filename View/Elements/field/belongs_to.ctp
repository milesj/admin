<?php
$rendered = false;

foreach ($data['belongsTo'] as $foreignModel => $className) {
	$belongsTo = $this->Admin->introspect($className);

	if (!empty($result[$foreignModel][$belongsTo->primaryKey])) {

		echo $this->Html->link($result[$foreignModel][$belongsTo->displayField], array(
			'plugin' => 'admin',
			'controller' => 'crud',
			'action' => 'read',
			'model' => $belongsTo->urlSlug,
			$value
		), array(
			'class' => 'belongs-to'
		));

		$rendered = true;
		break;
	}
}

if (!$rendered) { ?>

	<span class="belongs-to text-error">INVALID ASSOC: <?php echo $value; ?></span>

<?php }