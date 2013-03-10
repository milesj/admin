
<div class="action-buttons">
	<?php
	echo $this->Html->link('<span class="icon-cogs icon-white"></span> ' . __('Analyze Models'),
		array('controller' => 'admin', 'action' => 'analyze'),
		array('class' => 'btn btn-info btn-large', 'escape' => false)); ?>
</div>

<h2 style="margin-top: 0"><?php echo __('Dashboard'); ?></h2>

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
							<?php echo $this->Admin->outputIconTitle($model); ?>

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