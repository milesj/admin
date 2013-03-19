<?php if ($options = $this->Admin->getModelCallbacks($model)) { ?>

<div class="btn-group">
	<button data-toggle="dropdown" class="btn btn-large dropdown-toggle">
		<span class="icon-cog"></span>
		<?php echo __('Process'); ?>
		<span class="caret"></span>
	</button>

	<ul class="dropdown-menu">
		<?php foreach ($options as $method => $title) { ?>
			<li>
				<?php echo $this->Html->link(__($title, $model->singularName), array(
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