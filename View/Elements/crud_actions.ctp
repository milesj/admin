
<div class="action-buttons">
	<?php
	if ($this->action === 'index') {
		echo $this->Html->link('<span class="icon-filter icon-white"></span> ' . __('Filter'),
			'javascript:;', array('class' => 'btn btn-info btn-large', 'escape' => false, 'onclick' => "$('#filters').toggle()"));
	}

	if ($this->action !== 'create') {
		echo $this->Html->link('<span class="icon-plus icon-white"></span> ' . __('Add %s', $model->singularName),
			array('action' => 'create', 'model' => $model->urlSlug),
			array('class' => 'btn btn-primary btn-large', 'escape' => false));
	}

	if ($this->action === 'read' && $model->admin['editable']) {
		echo $this->Html->link('<span class="icon-edit icon-white"></span> ' . __('Edit %s', $model->singularName),
			array('action' => 'update', $result[$model->alias][$model->primaryKey], 'model' => $model->urlSlug),
			array('class' => 'btn btn-success btn-large', 'escape' => false));
	}

	if (in_array($this->action, array('read', 'update')) && $model->admin['deletable']) {
		echo $this->Html->link('<span class="icon-remove icon-white"></span> ' . __('Delete %s', $model->singularName),
			array('action' => 'delete', $result[$model->alias][$model->primaryKey], 'model' => $model->urlSlug),
			array('class' => 'btn btn-danger btn-large', 'escape' => false));
	} ?>
</div>