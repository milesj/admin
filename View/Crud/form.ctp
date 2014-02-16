<?php
if ($this->action === 'create') {
    $pageTitle = __d('admin', 'Add %s', $model->singularName);
    $result = null;
} else {
    $pageTitle = __d('admin', 'Edit %s', $model->singularName);
}

$this->Admin->setBreadcrumbs($model, $result, $this->action); ?>

<div class="title">
    <?php echo $this->element('crud/actions'); ?>

    <h2><?php echo $this->Admin->outputIconTitle($model, $pageTitle); ?></h2>
</div>

<div class="container">
    <?php
    echo $this->Form->create($model->alias, array('class' => 'form--horizontal', 'type' => 'file'));
    echo $this->element('crud/form_fields');
    echo $this->element('crud/form_extra');
    echo $this->element('form_actions');
    echo $this->Form->end(); ?>
</div>