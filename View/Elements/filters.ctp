
<div id="filters" class="form-filters" style="display: none">
    <?php
    $controller = $this->request->controller;
    $url = array('controller' => $controller, 'action' => 'proxy');
    $reset = array('controller' => $controller, 'action' => 'index');

    if ($controller === 'crud') {
        $url['model'] = $model->urlSlug;
        $reset['model'] = $model->urlSlug;
    }

    echo $this->Form->create($model->alias, array(
        'url' => $url,
        'class' => 'form--inline')
    );

    foreach ($model->fields as $field => $data) {
        if (in_array($field, $model->admin['fileFields'])) {
            continue;
        }

        $classes = array($data['type']);

        if (isset($this->data[$model->alias][$field]) && $this->data[$model->alias][$field] !== '') {
            $classes[] = 'has-warning';
        } ?>

        <div class="field <?php echo implode(' ', $classes); ?>">
            <?php $label = $data['title'];

            if (!empty($data['belongsTo'])) {
                $label .= ' <span class="fa fa-search js-tooltip" data-tooltip="' . __d('admin', 'Belongs To Lookup') . '"></span>';
            }

            echo $this->Form->label($field, $label, array('class' => 'field-label', 'escape' => false)); ?>

            <div class="input-group round">
                <?php
                // Belongs to is the only special case
                if (!empty($data['belongsTo'])) {
                    echo $this->element('Admin.input/belongs_to', array(
                        'field' => $field,
                        'data' => $data
                    ));

                // Display a comparison dropdown for filters
                } else if (in_array($data['type'], array('integer', 'datetime'))) {
                    $compValue = isset($this->data[$model->alias][$field . '_filter']) ? $this->data[$model->alias][$field . '_filter'] : '='; ?>

                    <div class="button-group">
                        <button type="button" data-dropdown="#filter-<?php echo $field; ?>" class="button js-dropdown">
                            <?php echo $compValue; ?>
                        </button>

                        <ul class="dropdown" id="filter-<?php echo $field; ?>">
                            <li><a href="javascript:;" data-filter="="><?php echo __d('admin', 'Equals'); ?></a></li>
                            <li><a href="javascript:;" data-filter="!="><?php echo __d('admin', 'Not Equals'); ?></a></li>
                            <li><a href="javascript:;" data-filter=">"><?php echo __d('admin', 'Greater Than'); ?></a></li>
                            <li><a href="javascript:;" data-filter=">="><?php echo __d('admin', 'Greater Than or Equal'); ?></a></li>
                            <li><a href="javascript:;" data-filter="<"><?php echo __d('admin', 'Less Than'); ?></a></li>
                            <li><a href="javascript:;" data-filter="<="><?php echo __d('admin', 'Less Than or Equal'); ?></a></li>
                        </ul>
                    </div>

                    <?php
                    echo $this->Form->input($field . '_filter', array(
                        'type' => 'hidden',
                        'div' => false,
                        'error' => false,
                        'label' => false,
                        'value' => $compValue
                    ));

                    echo $this->element('Admin.input/filter', array(
                        'field' => $field,
                        'data' => $data
                    ));
                } else {
                    echo $this->element('Admin.input/filter', array(
                        'field' => $field,
                        'data' => $data
                    ));
                } ?>
            </div>
        </div>

    <?php } ?>

    <div class="form-actions">
        <button type="submit" class="button is-info">
            <?php echo __d('admin', 'Filter'); ?>
        </button>

        <a href="<?php echo $this->Html->url($reset); ?>" class="button">
            <?php echo __d('admin', 'Reset'); ?>
        </a>
    </div>

    <script type="text/javascript">
        $(function() {
            $('#filter-toggle').click(Admin.filterToggle);

            Admin.filterComparisons();

            <?php if (!empty($this->request->params['named'])) { ?>
                Admin.filterToggle();
            <?php } ?>
        });
    </script>

    <?php echo $this->Form->end(); ?>
</div>