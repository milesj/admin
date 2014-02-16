<?php
$this->Breadcrumb->add(__d('admin', 'Reports'), array('controller' => 'reports', 'action' => 'index'));
$this->Breadcrumb->add($this->Admin->getDisplayField($model, $result), array('controller' => 'reports', 'action' => 'read', $result[$model->alias][$model->primaryKey]));

$itemModel = $this->Admin->introspect($result[$model->alias]['model']); ?>

<div class="title">
    <h2><?php echo $this->Admin->outputIconTitle($model, $this->Admin->getDisplayField($model, $result)); ?></h2>
</div>

<div class="container">
    <?php echo $this->element('crud/read_table');

    if (!empty($item)) { ?>

        <div class="reported-item">
            <h3><?php echo __d('admin', 'Reported %s', $itemModel->singularName); ?></h3>

            <?php echo $this->element('crud/read_table', array(
                'result' => $item,
                'model' => $itemModel
            )); ?>
        </div>

    <?php } ?>
</div>

<?php if ($result[$model->alias]['status'] == ItemReport::PENDING) {
    if (!empty($item)) {
        $options = $this->Admin->getModelCallbacks($itemModel);

        if ($this->Admin->hasAccess($itemModel, 'delete')) {
            $options['delete_item'] = __d('admin', 'Delete %s', $itemModel->singularName);
        }
    } else {
        $options = array();
    }

    $options['invalid_report'] = __d('admin', 'Mark As Invalid');

    echo $this->Form->create($model->alias, array('class' => 'form--horizontal')); ?>

    <div class="form-actions">
        <div class="redirect-to">
            <?php echo $this->Form->input('report_action', array(
                'div' => false,
                'class' => 'input',
                'options' => $options
            )); ?>
        </div>

        <?php if ($config['Admin']['logActions']) { ?>
            <div class="log-comment">
                <?php echo $this->Form->input('log_comment', array(
                    'div' => false,
                    'maxlength' => 255,
                    'required' => true,
                    'class' => 'input'
                )); ?>
            </div>
        <?php } ?>

        <button type="submit" class="button large is-error">
            <span class="fa fa-cog icon-white"></span>
            <?php echo __d('admin', 'Process Report'); ?>
        </button>
    </div>

    <?php echo $this->Form->end();
}