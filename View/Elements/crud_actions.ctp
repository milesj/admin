<?php
// Generate a process list based off behaviors
$options = array();

if ($behaviors = Configure::read('Admin.behaviorProcesses')) {
	foreach ($behaviors as $behavior => $processes) {
		if (!$model->Behaviors->loaded($behavior)) {
			continue;
		}

		foreach ($processes as $method => $process) {
			$options[] = array(
				'behavior' => $behavior,
				'method' => $method,
				'process' => $process
			);
		}
	}
} ?>

<div class="action-buttons">
	<?php
	if ($this->action === 'index') {
		echo $this->Html->link('<span class="icon-filter icon-white"></span> ' . __('Filter'),
			'javascript:;', array('class' => 'btn btn-info btn-large', 'escape' => false, 'onclick' => "$('#filters').toggle()"));

		if ($options) {
			ksort($options); ?>

		<div class="btn-group">
			<button data-toggle="dropdown" class="btn btn-large dropdown-toggle">
				<span class="icon-cog"></span>
				<?php echo __('Process'); ?>
				<span class="caret"></span>
			</button>

			<ul class="dropdown-menu">
				<?php foreach ($options as $option) { ?>
					<li>
						<?php echo $this->Html->link($option['process'], array(
							'action' => 'process',
							Inflector::underscore($option['behavior']),
							$option['method'],
							'model' => $model->urlSlug
						)); ?>
					</li>
				<?php } ?>
			</ul>
		</div>

		<?php }
	}

	if ($this->action !== 'create' && $this->Admin->hasAccess($model->qualifiedName, 'create')) {
		echo $this->Html->link('<span class="icon-pencil icon-white"></span> ' . __('Add %s', $model->singularName),
			array('action' => 'create', 'model' => $model->urlSlug),
			array('class' => 'btn btn-primary btn-large', 'escape' => false));
	}

	if ($this->action === 'read' && $this->Admin->hasAccess($model->qualifiedName, 'update') && $model->admin['editable']) {
		echo $this->Html->link('<span class="icon-edit icon-white"></span> ' . __('Edit %s', $model->singularName),
			array('action' => 'update', $result[$model->alias][$model->primaryKey], 'model' => $model->urlSlug),
			array('class' => 'btn btn-success btn-large', 'escape' => false));
	}

	if (in_array($this->action, array('read', 'update')) && $this->Admin->hasAccess($model->qualifiedName, 'delete') && $model->admin['deletable']) {
		echo $this->Html->link('<span class="icon-remove icon-white"></span> ' . __('Delete %s', $model->singularName),
			array('action' => 'delete', $result[$model->alias][$model->primaryKey], 'model' => $model->urlSlug),
			array('class' => 'btn btn-danger btn-large', 'escape' => false));
	} ?>
</div>