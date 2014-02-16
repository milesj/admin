<?php if ($dependencies = $this->Admin->getDependencies($model)) {
    $excludeDeps = array(); ?>

    <p><?php echo __d('admin', 'The associated records in the following models will also be deleted.'); ?></p>

    <div class="notice is-warning">
        <?php echo $this->Admin->loopDependencies($dependencies, $excludeDeps); ?>
    </div>

<?php } ?>