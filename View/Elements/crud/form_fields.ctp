<fieldset class="is-legendless">
    <?php // Loop over primary model fields
    foreach ($model->fields as $field => $data) {
        if (($this->action === 'create' && $field === $model->primaryKey) || in_array($field, $model->admin['hideFields'])) {
            continue;
        }

        echo $this->element('Admin.input', array(
            'field' => $field,
            'data' => $data
        ));
    } ?>
</fieldset>