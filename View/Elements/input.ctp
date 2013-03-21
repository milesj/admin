<?php
$classes = array($data['type']);
$hasError = isset($model->validationErrors[$field]);
$isRequired = false;

if ($hasError) {
	$classes[] = 'error';
}

if (isset($model->validate[$field])) {
	$isRequired = true;

	if (isset($model->validate[$field]['allowEmpty']) && $model->validate[$field]['allowEmpty']) {
		$isRequired = false;
	} else if (isset($model->validate[$field]['required'])) {
		$isRequired = $model->validate[$field]['required'];
	}

	if ($isRequired) {
		$classes[] = 'required';
	}
}

if ($hasError) {
	$classes[] = 'text-error';
} else if ($isRequired) {
	$classes[] = 'text-info';
} ?>

<div class="control-group <?php echo implode(' ', $classes); ?>">
	<?php echo $this->Form->label($field, $data['title'], array('class' => 'control-label')); ?>

	<div class="controls">
		<?php
		$element = 'default';

		if (!empty($data['belongsTo'])) {
			$element = 'belongs_to';

		} else if (!empty($data['habtm'])) {
			$element = 'has_and_belongs_to_many';

		} else if ($field === 'id') {
			$element = 'id';

		} else if (in_array($field, $model->admin['fileFields'])) {
			$element = 'file';

		} else if (in_array($data['type'], array('datetime', 'date', 'time'))) {
			$element = 'datetime';
		}

		echo $this->element('input/' . $element, array(
			'field' => $field,
			'data' => $data
		));

		// Show a null checkbox for fields that support it
		if (isset($data['null']) && $data['null']) {
			if (isset($this->data[$model->alias][$field])) {
				$null = $this->data[$model->alias][$field];
				$checked = ($null === null || $null === '');
			} else {
				$checked = ($data['default'] === null);
			} ?>

			<div class="controls-null">
				<?php echo $this->Form->input($field . '_null', array(
					'type' => 'checkbox',
					'checked' => $checked,
					'div' => false,
					'error' => false,
					'label' => __d('admin', 'Null?')
				)); ?>
			</div>

		<?php } ?>
	</div>
</div>