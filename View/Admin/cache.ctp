<?php
$this->Breadcrumb->add(__d('admin', 'Cache'), array('controller' => 'admin', 'action' => 'cache')); ?>

<div class="title">
	<?php echo $this->element('admin/actions'); ?>

	<h2><?php echo __d('admin', 'Cache'); ?></h2>
</div>

<div class="container">
	<div class="grid" id="grid">

		<?php foreach ($configuration as $group => $keys) {
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