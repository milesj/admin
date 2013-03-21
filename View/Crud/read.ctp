<?php
$this->Admin->setBreadcrumbs($model, $result, $this->action);

echo $this->element('crud/actions'); ?>

<h2><?php echo $this->Admin->outputIconTitle($model, $this->Admin->getDisplayField($model, $result)); ?></h2>

<div class="row-fluid">
	<?php echo $this->element('crud/read_table'); ?>
</div>

<?php // Loop over the types of associations
$properties = array(
	'hasOne' => 'Has One',
	'hasMany' => 'Has Many',
	'hasAndBelongsToMany' => 'Has and Belongs to Many'
);

foreach ($properties as $property => $title) {
	$associations = array();

	foreach ($model->{$property} as $alias => $assoc) {
		if ($property === 'hasOne') {
			$foreignModel = $this->Admin->introspect($assoc['className']);

			if (!empty($result[$alias][$foreignModel->primaryKey])) {
				$associations[$alias] = $assoc;
			}
		} else if (!empty($result[$alias])) {
			$associations[$alias] = $assoc;
		}
	}

	if ($associations) { ?>

	<div class="row-fluid">
		<h3 class="text-info"><?php echo __d('admin', $title); ?></h3>

		<?php // Loop over the model relations
		foreach ($associations as $alias => $assoc) {
			echo $this->element('crud/' . Inflector::underscore($property), array(
				'alias' => $alias,
				'assoc' => $assoc,
				'results' => $result[$alias]
			));
		} ?>
	</div>

<?php } } ?>