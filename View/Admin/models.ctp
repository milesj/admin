<?php
$this->Breadcrumb->add(__d('admin', 'Models'), array('controller' => 'admin', 'action' => 'models')); ?>

<div class="title">
    <?php echo $this->element('admin/actions'); ?>

    <h2><?php echo __d('admin', 'Models'); ?></h2>
</div>

<div class="container">
    <table class="table has-hover">
        <thead>
            <tr>
                <th><?php echo __d('admin', 'Model'); ?></th>
                <th class="js-tooltip" data-tooltip="<?php echo __d('admin', 'Primary Key'); ?>"><?php echo __d('admin', 'PK'); ?></span></th>
                <th><?php echo __d('admin', 'Display Field'); ?></th>
                <th><?php echo __d('admin', 'Database'); ?></th>
                <th><?php echo __d('admin', 'Table'); ?></th>
                <th><?php echo __d('admin', 'Schema'); ?></th>
                <th><?php echo __d('admin', 'Behaviors'); ?></th>
                <th><?php echo __d('admin', 'Belongs To'); ?></th>
                <th><?php echo __d('admin', 'Has One'); ?></th>
                <th><?php echo __d('admin', 'Has Many'); ?></th>
                <th class="js-tooltip" data-tooltip="<?php echo __d('admin', 'Has and Belongs to Many'); ?>"><?php echo __d('admin', 'HABTM'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($plugins as $plugin) { ?>

                <tr class="table-divider">
                    <td colspan="11"><b><?php echo $plugin['title']; ?></b></td>
                </tr>

                <?php foreach ($plugin['models'] as $model) {
                    $object = $this->Admin->introspect($model['id']) ?>

                <tr>
                    <td>
                        <?php echo $this->Html->link($model['alias'], array(
                            'controller' => 'crud',
                            'action' => 'index',
                            'model' => $model['url']
                        )); ?>
                    </td>
                    <td><?php echo $object->primaryKey; ?></td>
                    <td>
                        <?php if ($object->displayField == $object->primaryKey) { ?>
                            <span class="label is-warning"><?php echo ('N/A'); ?></span>
                        <?php } else {
                            echo $object->displayField;
                        } ?>
                    </td>
                    <td><?php echo $object->useDbConfig; ?></td>
                    <td><?php echo $object->tablePrefix . $object->useTable; ?></td>
                    <td><?php echo implode(', ', $this->Admin->normalizeArray($object->schema(), false)); ?></td>
                    <td><?php echo implode(', ', $this->Admin->normalizeArray($object->actsAs)); ?></td>
                    <td><?php echo implode(', ', $this->Admin->normalizeArray($object->belongsTo)); ?></td>
                    <td><?php echo implode(', ', $this->Admin->normalizeArray($object->hasOne)); ?></td>
                    <td><?php echo implode(', ', $this->Admin->normalizeArray($object->hasMany)); ?></td>
                    <td><?php echo implode(', ', $this->Admin->normalizeArray($object->hasAndBelongsToMany)); ?></td>
                </tr>

            <?php } } ?>
        </tbody>
    </table>
</div>