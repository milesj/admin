<?php if ($options = $this->Admin->getModelCallbacks($model)) { ?>

<div class="button-group round">
	<button type="button" data-toggle="#process-model" class="button js-toggle">
		<span class="icon-cog"></span>
		<?php echo __d('admin', 'Process'); ?>
		<span class="caret-down"></span>
	</button>

	<ul class="dropdown dropdown--right" id="process-model">
		<?php foreach ($options as $method => $title) { ?>
			<li>
				<?php echo $this->Html->link(__d('admin', $title, $model->singularName), array(
					'controller' => 'crud',
					'action' => 'process_model',
					$model->id,
					$method,
					'model' => $model->urlSlug
				)); ?>
			</li>
		<?php } ?>
	</ul>
</div>

<?php }