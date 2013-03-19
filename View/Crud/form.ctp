<?php
if ($this->action === 'create') {
	$pageTitle = __('Add %s', $model->singularName);
	$result = null;
} else {
	$pageTitle = __('Edit %s', $model->singularName);
}

$this->Admin->setBreadcrumbs($model, $result, $this->action);

echo $this->element('crud/actions'); ?>

<h2><?php echo $this->Admin->outputIconTitle($model, $pageTitle); ?></h2>

<?php echo $this->Form->create($model->alias, array('class' => 'form-horizontal', 'file' => true)); ?>

<fieldset>
	<?php // Loop over primary model fields
	foreach ($model->fields as $field => $data) {
		if (($this->action === 'create' && $field === $model->primaryKey) || in_array($field, $model->admin['hideFields'])) {
			continue;
		}

		echo $this->element('input', array(
			'field' => $field,
			'data' => $data
		));
	} ?>
</fieldset>

<?php // Display HABTM fields
$habtm = array();

foreach ($model->hasAndBelongsToMany as $alias => $assoc) {
	if ($assoc['showInForm']) {
		$habtm[$alias] = $assoc;
	}
}

if ($habtm) { ?>

	<fieldset>
		<legend><?php echo __('Associate With'); ?></legend>

		<?php foreach ($habtm as $alias => $assoc) {
			$assoc['type'] = 'relation';
			$assoc['title'] = $alias;
			$assoc['habtm'] = true;

			echo $this->element('input', array(
				'field' => $alias,
				'data' => $assoc
			));
		} ?>
	</fieldset>

<?php }

echo $this->element('form_actions');
echo $this->Form->end();