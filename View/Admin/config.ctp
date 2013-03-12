<?php
$this->Breadcrumb->add(__('Configuration'), array('controller' => 'admin', 'action' => 'config'));

echo $this->element('admin_actions'); ?>

<h2><?php echo __('Configuration'); ?></h2>

<div class="row-fluid config-grid" id="grid">
	<?php foreach ($configuration as $group => $keys) { ?>

		<div class="well">
			<h3><?php echo $group; ?></h3>

			<?php echo $this->element('autobox', array(
				'data' => $keys,
				'parent' => $group . '.',
				'depth' => 1
			)) ?>
		</div>
	<?php } ?>
</div>