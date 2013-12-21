<?php
$overwriteValue = isset($overwriteValue) ? $overwriteValue : null;

echo $this->Form->input($field, array(
    'div' => false,
    'label' => false,
    'class' => 'input',
    'multiple' => true,
    'default' => $overwriteValue
));