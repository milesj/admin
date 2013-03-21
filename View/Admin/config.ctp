<?php
$this->Breadcrumb->add(__d('admin', 'Configuration'), array('controller' => 'admin', 'action' => 'config'));

echo $this->element('admin/actions'); ?>

<h2><?php echo __d('admin', 'Configuration'); ?></h2>

<div class="row-fluid config-grid" id="grid">
	<?php foreach ($configuration as $group => $keys) { ?>

		<div class="well">
			<h3><?php echo $group; ?></h3>

			<?php echo $this->element('admin/config', array(
				'data' => $keys,
				'parent' => $group . '.',
				'depth' => 0
			)) ?>
		</div>
	<?php } ?>
</div>