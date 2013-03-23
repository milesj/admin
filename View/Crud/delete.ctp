<?php
$this->Admin->setBreadcrumbs($model, $result, $this->action);

$id = $result[$model->alias][$model->primaryKey];
$displayField = $this->Admin->getDisplayField($model, $result); ?>

<h2><?php echo $this->Admin->outputIconTitle($model, __d('admin', 'Delete %s', $model->singularName)); ?></h2>

<p><?php echo __d('admin', 'Are you sure you want to delete %s?', $this->Html->link($displayField, array('action' => 'read', $id, 'model' => $model->urlSlug))); ?></p>

<?php echo $this->element('crud/dependencies');

// Confirm delete form
echo $this->Form->create($model->alias, array('class' => 'form-horizontal'));
echo $this->element('form_actions');
echo $this->Form->end(); ?>