<?php
$options = array(
    'index' => __d('admin', 'List of %s', $model->pluralName),
    'create' => __d('admin', 'Create new %s', $model->singularName)
);

if ($this->action !== 'delete') {
    $options = array_merge(array(
        'update' => __d('admin', 'Continue Editing'),
        'read' => __d('admin', '%s Overview', $model->singularName)
    ), $options);
}

foreach ($model->belongsTo as $parentName => $parentAssoc) {
    if (!empty($this->params['named'][$parentAssoc['foreignKey']])) {
        $options = array_merge(array(
            'parent' => __d('admin', 'Back to parent %s', $parentName),
        ), $options);

        echo $this->Form->input('redirect_to_model', array('type' => 'hidden', 'value' => $parentName));
        break;
    }
} ?>

<div class="form-actions">
    <div class="redirect-to">
        <?php echo $this->Form->input('redirect_to', array(
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
                'required' => in_array($this->action, array('update', 'delete')),
                'class' => 'input'
            )); ?>
        </div>

    <?php }

    if ($this->action === 'delete') { ?>

        <button type="submit" class="button large is-error">
            <span class="fa fa-times icon-white"></span>
            <?php echo __d('admin', 'Yes, Delete'); ?>
        </button>

    <?php } else { ?>

        <button type="submit" class="button large is-success">
            <span class="fa fa-edit icon-white"></span>
            <?php echo __d('admin', $this->action === 'create' ? 'Create' : 'Update'); ?>
        </button>

        <button type="reset" class="button large is-info">
            <span class="fa fa-undo icon-white"></span>
            <?php echo __d('admin', 'Reset'); ?>
        </button>

        <a href="<?php echo $this->Html->url(array('action' => 'index', 'model' => $model->urlSlug)); ?>" class="button large">
            <span class="fa fa-ban"></span>
            <?php echo __d('admin', 'Cancel'); ?>
        </a>

    <?php } ?>
</div>