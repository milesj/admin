<?php
$this->Breadcrumb->add(__d('admin', 'Configuration'), array('controller' => 'admin', 'action' => 'config')); ?>

<div class="title">
	<?php echo $this->element('admin/actions'); ?>

	<h2><?php echo __d('admin', 'Configuration'); ?></h2>
</div>

<div class="container">
	<div class="grid" id="grid">

		<?php foreach ($configuration as $group => $keys) {
			if (!is_array($keys)) {
				continue;
			}

			ksort($keys); ?>

			<div class="panel">
				<div class="panel-heading">
					<h3 class="panel-title"><?php echo $group; ?></h3>
				</div>

				<?php echo $this->element('admin/config', array(
					'data' => $keys,
					'parent' => $group . '.',
					'depth' => 0
				)) ?>
			</div>

		<?php } ?>

	</div>
</div>