<?php
$id = $result[$model->alias][$model->primaryKey];
$displayField = $this->Admin->getDisplayField($result, $model);

$this->Breadcrumb->add(__('Dashboard'), array('controller' => 'admin', 'action' => 'index'));
$this->Breadcrumb->add($model->pluralName, array('action' => 'index', 'model' => $this->params['model']));
$this->Breadcrumb->add($displayField, array('action' => 'read', $id, 'model' => $this->params['model']));
$this->Breadcrumb->add(__('Delete'), array('action' => 'delete', $id, 'model' => $this->params['model']));

// Gather dependencies
$dependencies = array();

foreach (array($model->hasOne, $model->hasMany, $model->hasAndBelongsToMany) as $assocGroup) {
	foreach ($assocGroup as $assoc) {
		// hasOne, hasMany
		if (isset($assoc['dependent']) && $assoc['dependent']) {
			$dependencies[] = $assoc['className'];

		// hasAndBelongsToMany
		} else if (isset($assoc['joinTable'])) {
			$dependencies[] = $assoc['with'];
		}
	}
}

$dependencies = array_unique($dependencies); ?>

<h2><?php echo __('Delete %s', $model->singularName); ?></h2>

<p>
	<?php echo __('Are you sure you want to delete %s?', $this->Html->link($displayField, array('action' => 'read', $id, 'model' => $this->params['model']))) . ' ';

	if ($dependencies) {
		echo __('If so, the records in the following associated tables will also be deleted.');
	} ?>
</p>

<?php // List out dependencies as a warning
if ($dependencies) { ?>

	<div class="alert alert-block">
		<ul>
			<?php foreach ($dependencies as $dependent) { ?>
				<li><?php echo $dependent; ?></li>
			<?php } ?>
		</ul>
	</div>

<?php }

// Confirm delete form
echo $this->Form->create($model->alias); ?>

	<div class="well align-center">
		<button type="submit" class="btn btn-large btn-danger">
			<span class="icon-remove icon-white"></span>
			<?php echo __('Yes, Delete'); ?>
		</button>
	</div>

<?php echo $this->Form->end(); ?>