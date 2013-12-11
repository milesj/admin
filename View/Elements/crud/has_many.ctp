<?php
$foreignModel = $this->Admin->introspect($assoc['className']);
$fields = $this->Admin->filterFields($foreignModel, $assoc['fields']); ?>

<div class="panel has-many">
    <div class="panel-head">
        <div class="action-buttons">
            <?php
            if ($this->Admin->hasAccess($foreignModel->qualifiedName, 'create')) {
                echo $this->Html->link('<span class="fa fa-pencil icon-white"></span> ' . __d('admin', 'Add %s', $foreignModel->singularName),
                    array('action' => 'create', 'model' => $foreignModel->urlSlug, $assoc['foreignKey'] => $result[$model->alias][$model->primaryKey]),
                    array('class' => 'button small is-info', 'escape' => false));
            }
            ?>
        </div>

        <h5>
            <?php echo $this->Admin->outputAssocName($foreignModel, $alias, $assoc['className']); ?>

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

    <?php if(!empty($results)) { ?>
        <div class="panel-body">
            <table class="table has-hover is-clickable">
                <thead>
                    <tr>
                        <?php foreach ($fields as $field => $data) { ?>
                            <th class="col-<?php echo $field; ?> type-<?php echo $data['type']; ?>">
                                <span><?php echo $data['title']; ?></span>
                            </th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $result) { ?>

                        <tr>
                            <?php foreach ($fields as $field => $data) { ?>

                                <td class="col-<?php echo $field; ?> type-<?php echo $data['type']; ?>">
                                    <?php echo $this->element('Admin.field', array(
                                        'result' => $result,
                                        'field' => $field,
                                        'data' => $data,
                                        'value' => $result[$field],
                                        'model' => $foreignModel
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