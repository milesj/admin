<?php
$this->Breadcrumb->add(__d('admin', 'Logs'), array('controller' => 'logs', 'action' => 'index')); ?>

<div class="title">
    <?php echo $this->element('logs/actions'); ?>

    <h2><?php echo $model->pluralName; ?></h2>
</div>

<div class="container">
    <?php echo $this->element('pagination', array('class' => 'top')); ?>

    <table id="table" class="table has-hover is-sortable is-clickable">
        <thead>
            <tr>
                <?php foreach (array('id', 'user_id', 'action', 'item', 'comment', 'created') as $field) { ?>
                    <th class="col-<?php echo $field; ?>">
                        <?php echo $this->Paginator->sort($field, $model->fields[$field]['title']); ?>
                    </th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($results) {
                foreach ($results as $result) {
                    $id = $result[$model->alias][$model->primaryKey];
                    $foreignModel = $this->Admin->introspect($result[$model->alias]['model']);
                    $userModel = $this->Admin->introspect(USER_MODEL); ?>

                <tr>
                    <?php echo $this->element('field_cell', array(
                        'result' => $result,
                        'field' => 'id',
                        'data' => $model->fields['id']
                    )); ?>

                    <td colspan="3">
                        <?php
                        $message = 'action_log.' . mb_strtolower($result[$model->alias]['action_enum']);
                        $params = array();

                        // Grab the user
                        $params[] = $this->Html->link($result['User'][$userModel->displayField], array(
                            'controller' => 'crud',
                            'action' => 'read',
                            $result[$model->alias]['user_id'],
                            'model' => $userModel->urlSlug
                        ));

                        // CRUD specific
                        if (in_array($result[$model->alias]['action'], array(ActionLog::CREATE, ActionLog::READ, ActionLog::UPDATE, ActionLog::DELETE, ActionLog::PROCESS))) {
                            $params[] = mb_strtolower($foreignModel->singularName);
                        }

                        // Grab the item
                        if ($foreignKey = $result[$model->alias]['foreign_key']) {
                            $title = $result[$model->alias]['item'];

                            if (!$title) {
                                $title = $foreignKey;
                            }

                            $params[] = $this->Html->link($title, array(
                                'controller' => 'crud',
                                'action' => 'read',
                                $foreignKey,
                                'model' => $foreignModel->urlSlug
                            ), array('class' => 'click-target'));

                        } else if ($foreignModel) {
                            $params[] = mb_strtolower($foreignModel->pluralName);
                        }

                        echo __d('admin', $message, $params); ?>
                    </td>

                    <?php foreach (array('comment', 'created') as $field) {
                        echo $this->element('field_cell', array(
                            'result' => $result,
                            'field' => $field,
                            'data' => $model->fields[$field]
                        ));
                    } ?>
                </tr>

                <?php }
            } else { ?>

                <tr>
                    <td colspan="6" class="no-results">
                        <?php echo __d('admin', 'No results to display'); ?>
                    </td>
                </tr>

            <?php } ?>
        </tbody>
    </table>

    <?php echo $this->element('pagination', array('class' => 'bottom')); ?>
</div>