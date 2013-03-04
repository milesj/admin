<?php
if (!empty($data['belongsTo']) && !empty($value)) {
	$element = 'belongs_to';

} else if ($field === $model->primaryKey) {
	$element = 'id';

} else if (in_array($field, $model->admin['imageFields'])) {
	$element = 'image';

} else {
	$element = $data['type'];
}

if ($value === null || $value === '') { ?>

	<div class="muted align-center">-</div>

<?php } else {
	echo $this->element('field/' . $element, array(
		'result' => $result,
		'field' => $field,
		'data' => $data,
		'value' => $value,
		'model' => $model
	));
}