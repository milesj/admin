<h2><?php echo __('Add %s', $model->singularName); ?></h2>

<?php
echo $this->Form->create($model->alias, array('class' => 'form-horizontal'));

foreach ($model->fields as $field) {
	if ($this->action === 'create' && $field === 'id') {
		continue;
	}

	$options = null;

	if (isset($model->enum[$field])) {
		$options = $model->enum[$field];
	}


	echo $this->Form->input($field, array(
		'options' => $options
	));
}

echo $this->Form->end();

debug($model); ?>