<?php if ($options = $this->Admin->getModelCallbacks($model)) { ?>

<div class="button-group round">
    <button type="button" data-drop="#process-model" class="button last js-drop">
        <span class="fa fa-cog"></span>
        <?php echo __d('admin', 'Process'); ?>
        <span class="caret-down"></span>
    </button>

    <ul class="drop--down reverse-align" id="process-model">
        <?php foreach ($options as $method => $title) { ?>
            <li>
                <?php echo $this->Html->link(__d('admin', $title, $model->singularName), array(
                    'controller' => 'crud',
                    'action' => 'process_model',
                    $model->id,
                    $method,
                    'model' => $model->urlSlug
                )); ?>
            </li>
        <?php } ?>
    </ul>
</div>

<?php }

if ($links = $this->Admin->getModelLinks($model)) { ?>

<div class="button-group round">
    <button type="button" data-drop="#links" class="button last js-drop">
        <span class="fa fa-link"></span>
        <?php echo __d('admin', 'Links'); ?>
        <span class="caret-down"></span>
    </button>

    <ul class="drop--down reverse-align" id="links">
        <?php foreach ($links as $title => $url) {
            if (!isset($url['plugin'])) {
                $url['plugin'] = false;
            } ?>

            <li>
                <?php echo $this->Html->link(
                    __d('admin', $title, $model->singularName),
                    $url + array($model->id),
                    array('target' => '_blank')
                ); ?>
            </li>
        <?php } ?>
    </ul>
</div>

<?php }