<?php
if (isset($this->data[$model->alias][$field])) {
	$null = $this->data[$model->alias][$field];
	$checked = ($null === null || $null === '');
} else {
	$checked = ($data['default'] === null);
} ?>

<div class="controls-null">
	<?php
	echo $this->Form->input($field . '_null', array(
		'type' => 'checkbox',
		'checked' => $checked,
		'div' => false,
		'error' => false,
		'label' => __('Null?')
	)); ?>
</div>