<div class="action-buttons">
    <?php
    if ($this->action === 'index') {
        echo $this->element('Admin.button/filter');
        echo $this->element('Admin.button/export');
        echo $this->element('Admin.button/process_behavior');
    }

    if ($this->action === 'read' || $this->action === 'update') {
        echo $this->element('Admin.button/process_model');
    }

    if ($this->action !== 'create' && $this->Admin->hasAccess($model->qualifiedName, 'create')) {
        echo $this->Html->link('<span class="fa fa-pencil icon-white"></span> ' . __d('admin', 'Add %s', $model->singularName),
            array('action' => 'create', 'model' => $model->urlSlug),
            array('class' => 'button is-info', 'escape' => false));
    }

    if ($this->action === 'read' && $this->Admin->hasAccess($model->qualifiedName, 'update') && $model->admin['editable']) {
        echo $this->Html->link('<span class="fa fa-edit icon-white"></span> ' . __d('admin', 'Edit %s', $model->singularName),
            array('action' => 'update', $result[$model->alias][$model->primaryKey], 'model' => $model->urlSlug),
            array('class' => 'button is-success', 'escape' => false));
    }

    if (in_array($this->action, array('read', 'update')) && $this->Admin->hasAccess($model->qualifiedName, 'delete') && $model->admin['deletable']) {
        echo $this->Html->link('<span class="fa fa-times icon-white"></span> ' . __d('admin', 'Delete %s', $model->singularName),
            array('action' => 'delete', $result[$model->alias][$model->primaryKey], 'model' => $model->urlSlug),
            array('class' => 'button is-error', 'escape' => false));
    } ?>
</div>