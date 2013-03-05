<?php
$hasError = isset($model->validationErrors[$field]);
$isRequired = false;
$classes = array($data['type']);

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
		if ($data['null']) { ?>

			<div class="controls-null">
				<?php
				echo $this->Form->input($field . '_null', array(
					'type' => 'checkbox',
					'checked' => (empty($this->data[$model->alias][$field]) && $data['default'] === null),
					'div' => false,
					'error' => false,
					'label' => __('Null?')
				)); ?>
			</div>

		<?php } ?>
	</div>
</div>