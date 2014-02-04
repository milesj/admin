<?php if ($options = $this->Admin->getBehaviorCallbacks($model)) { ?>

<div class="button-group round">
    <button type="button" data-drop="#process-behavior" class="button last js-drop">
        <span class="fa fa-cog"></span>
        <?php echo __d('admin', 'Process'); ?>
        <span class="caret-down"></span>
    </button>

    <ul class="drop--down reverse-align" id="process-behavior">
        <?php foreach ($options as $option) { ?>
            <li>
                <?php echo $this->Html->link(__d('admin', $option['title'], $model->singularName), array(
                    'controller' => 'crud',
                    'action' => 'process_behavior',
                    $option['behavior'],
                    $option['method'],
                    'model' => $model->urlSlug
                )); ?>
            </li>
        <?php } ?>
    </ul>
</div>

<?php }