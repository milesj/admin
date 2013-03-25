<?php

// If image is not allowed in this action, display a link
if (isset($model->admin['imageFields'][$field])) {
	if (!in_array($this->action, (array) $model->admin['imageFields'][$field])) {
		echo $this->element('Admin.field/file', array(
			'result' => $result,
			'field' => $field,
			'data' => $data,
			'value' => $value,
			'model' => $model
		));

		return;
	}
}

// Display image
echo $this->Html->image($value, array(
	'pathPrefix' => '',
	'alt' => '',
	'class' => 'image'
));