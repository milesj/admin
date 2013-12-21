<?php

$overwriteValue = isset($overwriteValue) ? $overwriteValue : null;

// Display a select menu of options
if (empty($typeAhead[$field])) {
    echo $this->Form->input($field, array(
        'div' => false,
        'label' => false,
        'empty' => ($this->action === 'index' || $data['null']),
        'class' => 'input',
        'type' => 'select',
        'value' => $overwriteValue
    ));

// Use a text box with type ahead via AJAX
} else {
    $value = $overwriteValue;
    $alias = $typeAhead[$field]['alias'];

    if (isset($this->data[$alias][$model->{$alias}->displayField])) {
        $value = $this->data[$alias][$model->{$alias}->displayField];
    } ?>

    <div class="field-type-ahead span-4">
        <?php
        echo $this->Form->input($field . '_type_ahead', array(
            'div' => false,
            'label' => false,
            'type' => 'text',
            'class' => 'input input-belongs-to span-12',
            'autocomplete' => 'off',
            'value' => $value,
            'default' => $data['default']
        ));

        echo $this->Form->input($field, array(
            'type' => 'hidden',
            'div' => false,
            'label' => false,
            'required' => false,
            'value' => $value
        )); ?>
    </div>

    <script type="text/javascript">
        $(function() {
            Admin.typeAhead(
                '<?php echo $this->Form->domId(); ?>',
                '<?php echo $this->Html->url(array('model' => Inflector::underscore($typeAhead[$field]['model']), 'action' => 'type_ahead')); ?>',
                <?php echo json_encode(array_filter(array($typeAhead[$field]['foreignKey'] => $model->id)), JSON_FORCE_OBJECT); ?>
            );
        });
    </script>
<?php }