<div class="action-buttons">
	<?php
	$canUpdate = ($this->Admin->hasAccess($model->qualifiedName, 'update') && $model->admin['editable']);

	if ($this->action === 'index') {
		echo $this->element('button/filter');
		echo $this->element('button/process_behavior');
	}

	if ($this->action === 'read' && $canUpdate) {
		echo $this->element('button/process_model');
	}

	if ($this->action !== 'create' && $this->Admin->hasAccess($model->qualifiedName, 'create')) {
		echo $this->Html->link('<span class="icon-pencil icon-white"></span> ' . __('Add %s', $model->singularName),
			array('action' => 'create', 'model' => $model->urlSlug),
			array('class' => 'btn btn-primary btn-large', 'escape' => false));
	}

	if ($this->action === 'read' && $canUpdate) {
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