<?php
$classes = array($data['type']);
$hasError = isset($model->validationErrors[$field]);
$isRequired = false;
$isEditor = in_array($field, $model->admin['editorFields']);
$validate = false;

if ($hasError) {
    $classes[] = 'has-error';
}

if ($isEditor) {
    $classes[] = 'is-editor';
}

if (isset($model->validate[$field])) {
    $validate = $model->validate[$field];
} else if (isset($model->validations['default'][$field])) {
    $validate = $model->validations['default'][$field];
}

if ($validate) {
    $isRequired = true;

    if (isset($validate['allowEmpty']) && $validate['allowEmpty']) {
        $isRequired = false;
    } else if (isset($validate['required'])) {
        $isRequired = $validate['required'];
    }

    if ($isRequired) {
        $classes[] = 'is-required';
    }
} ?>

<div class="field <?php echo implode(' ', $classes); ?>">
    <?php
    $label = $data['title'];

    if (!empty($data['belongsTo'])) {
        $label .= ' <span class="fa fa-search js-tooltip" data-tooltip="' . __d('admin', 'Belongs To Lookup') . '"></span>';
    }

    echo $this->Form->label($field, $label, array('class' => 'field-label col span-3', 'escape' => false)); ?>

    <div class="col span-7">
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

        // Value from named param
        $overwriteValue = null;

        if (!empty($this->params['named'][$field])) {
            $overwriteValue = $this->params['named'][$field];
        }

        echo $this->element('Admin.input/' . $element, array(
            'field' => $field,
            'data' => $data,
            'overwriteValue' => $overwriteValue
        ));

        // Show a null checkbox for fields that support it
        if (isset($data['null']) && $data['null'] && !$isRequired) {
            if (isset($this->data[$model->alias][$field])) {
                $null = $this->data[$model->alias][$field];
                $checked = ($null === null || $null === '');
            } else {
                $checked = ($data['default'] === null);
            } ?>

            <div class="field-null">
                <?php echo $this->Form->input($field . '_null', array(
                    'type' => 'checkbox',
                    'checked' => $checked,
                    'div' => false,
                    'error' => false,
                    'label' => __d('admin', 'Empty?')
                )); ?>
            </div>

        <?php } ?>
    </div>

    <?php // Include an element that may wrap the input with a wysiwyg
    if ($isEditor && $model->admin['editorElement']) {
        echo $this->element($model->admin['editorElement'], array(
            'inputId' => $this->Form->domId()
        ));
    } ?>
</div>
