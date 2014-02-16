<?php
echo $this->Html->link($value,
    array('controller' => 'crud', 'action' => 'read', $value, 'model' => $model->urlSlug),
    array('class' => 'click-target'));