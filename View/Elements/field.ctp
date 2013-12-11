<?php
if (!empty($data['belongsTo']) && !empty($value) || $data['type'] === 'relation') {
    $element = 'belongs_to';

} else if ($field === $model->primaryKey) {
    $element = 'id';

} else if (in_array($field, $model->admin['imageFields']) || isset($model->admin['imageFields'][$field])) {
    $element = 'image';

} else if (in_array($field, $model->admin['fileFields'])) {
    $element = 'file';

} else {
    $element = $data['type'];
}

if ($value === null || $value === '') { ?>

    <div class="text-muted align-center">-</div>

<?php } else {
    echo $this->element('Admin.field/' . $element, array(
        'result' => $result,
        'field' => $field,
        'data' => $data,
        'value' => $value,
        'model' => $model
    ));
}