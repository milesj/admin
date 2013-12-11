<?php
$this->Breadcrumb->add(__d('admin', 'Configuration'), array('controller' => 'admin', 'action' => 'config')); ?>

<div class="title">
    <?php echo $this->element('admin/actions'); ?>

    <h2><?php echo __d('admin', 'Configuration'); ?></h2>
</div>

<div class="container">
    <div class="panels js-matrix">

        <?php foreach ($configuration as $group => $keys) {
            if (!is_array($keys)) {
                continue;
            }

            ksort($keys); ?>

            <div class="panel">
                <div class="panel-head">
                    <h4><?php echo $group; ?></h4>
                </div>

                <div class="panel-body">
                    <?php echo $this->element('admin/config', array(
                        'data' => $keys,
                        'parent' => $group . '.',
                        'depth' => 0
                    )) ?>
                </div>
            </div>

        <?php } ?>

    </div>
</div>