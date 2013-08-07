<?php if ($options = $this->Admin->getBehaviorCallbacks($model)) { ?>

<div class="btn-group">
	<a href="javascript:;" data-toggle="dropdown" class="btn btn-default dropdown-toggle">
		<span class="icon-cog"></span>
		<?php echo __d('admin', 'Process'); ?>
		<span class="caret"></span>
	</a>

	<ul class="dropdown-menu">
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