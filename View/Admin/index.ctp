<?php
$this->Breadcrumb->add(__('Dashboard'), array('controller' => 'admin', 'action' => 'index')); ?>

<div class="row-fluid">
	<?php foreach ($plugins as $plugin) { ?>

		<div id="<?php echo $plugin['slug']; ?>" class="well span3">
			<h3><?php echo $plugin['title']; ?></h3>

			<ul class="nav nav-pills nav-stacked">
				<?php foreach ($plugin['models'] as $model) {
					$url = $this->Html->url(array(
						'controller' => 'crud',
						'action' => 'index',
						'model' => $model['url']
					)); ?>

					<li>
						<?php if ($model['installed']) {
							echo $this->Html->link('<span class="icon-plus"></span>', array(
								'controller' => 'crud',
								'action' => 'create',
								'model' => $model['url']
							), array(
								'title' => __('Add'),
								'class' => 'pull-right tip',
								'escape' => false
							));
						} ?>

						<a href="<?php echo $url; ?>">
							<?php echo $model['title']; ?>

							<?php if (!$model['installed']) { ?>
								<span class="label label-important tip" title="<?php echo __('Not Installed'); ?>">&nbsp;!&nbsp;</span>
							<?php } else { ?>
								<span class="muted">(<?php echo number_format($counts[$model['class']]); ?>)</span>
							<?php } ?>
						</a>
					</li>

				<?php } ?>
			</ul>
		</div>

	<?php } ?>
</div>