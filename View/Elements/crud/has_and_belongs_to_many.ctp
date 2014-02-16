<?php
$foreignModel = $this->Admin->introspect($assoc['className']);
$withModel = $this->Admin->introspect($assoc['with']);
$fields = $this->Admin->filterFields($withModel); ?>

<div class="panel habtm">
    <div class="panel-head">
        <div class="action-buttons">
            <?php
            if ($this->Admin->hasAccess($withModel->qualifiedName, 'create')) {
                echo $this->Html->link('<span class="fa fa-pencil icon-white"></span> ' . __d('admin', 'Add %s', $withModel->singularName),
                    array('action' => 'create', 'model' => $withModel->urlSlug, $assoc['foreignKey'] => $result[$model->alias][$model->primaryKey]),
                    array('class' => 'button small is-info', 'escape' => false));
            }
            ?>
        </div>

        <h5>
            <?php echo $this->Admin->outputAssocName($foreignModel, $alias, $assoc['className']); ?>
            <span class="text-muted">&rarr;</span>
            <?php echo $this->Admin->outputAssocName($withModel, $withModel->alias, $assoc['with']); ?>

            <?php if (isset($counts[$alias])) {
                $total = $counts[$alias];
                $count = $assoc['limit'] ?: count($results);

                if ($count > $total) {
                    $count = $total;
                } ?>

                <span class="text-muted">&mdash;</span> <?php echo __d('admin', '%s of %s', array($count, $total)); ?>
            <?php } ?>
        </h5>
    </div>

    <?php if (!empty($results)) { ?>
        <div class="panel-body">
            <table class="table has-hover is-clickable">
                <thead>
                    <tr>
                        <th>
                            <span><?php echo $alias; ?></span>
                        </th>

                        <?php foreach ($fields as $field => $data) {
                            if (mb_strpos($field, '_id') !== false) {
                                continue;
                            } ?>

                            <th class="col-<?php echo $field; ?> type-<?php echo $data['type']; ?>">
                                <span><?php echo $data['title']; ?></span>
                            </th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $result) { ?>

                        <tr>
                            <td>
                                <?php echo $this->Html->link($result[$foreignModel->displayField], array(
                                    'action' => 'read',
                                    'model' => $foreignModel->urlSlug,
                                    $result[$foreignModel->primaryKey]
                                )); ?>
                            </td>

                            <?php foreach ($fields as $field => $data) {
                                if (mb_strpos($field, '_id') !== false) {
                                    continue;
                                } ?>

                                <td class="col-<?php echo $field; ?> type-<?php echo $data['type']; ?>">
                                    <?php echo $this->element('Admin.field', array(
                                        'result' => $result,
                                        'field' => $field,
                                        'data' => $data,
                                        'value' => $result[$withModel->alias][$field],
                                        'model' => $withModel
                                    )); ?>
                                </td>

                            <?php } ?>
                        </tr>

                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</div>