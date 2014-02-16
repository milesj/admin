<?php
$this->Breadcrumb->add(__d('admin', 'Cache'), array('controller' => 'admin', 'action' => 'cache')); ?>

<div class="title">
    <?php echo $this->element('admin/actions'); ?>

    <h2><?php echo __d('admin', 'Cache'); ?></h2>
</div>

<div class="container">
    <div class="panels js-matrix">

        <?php foreach ($configuration as $group => $keys) {
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