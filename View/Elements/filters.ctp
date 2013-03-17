
<div id="filters" class="well well-small filters"<?php if (empty($this->request->params['named'])) { ?> style="display: none"<?php } ?>>
	<?php
	if ($this->params['controller'] === 'reports') {
		$url = array('controller' => 'reports', 'action' => 'proxy');
		$reset = array('controller' => 'reports', 'action' => 'index');
	} else {
		$url = array('controller' => 'crud', 'action' => 'proxy', 'model' => $model->urlSlug);
		$reset = array('controller' => 'crud', 'action' => 'index', 'model' => $model->urlSlug);
	}

	echo $this->Form->create($model->alias, array(
		'url' => $url,
		'class' => 'form-inline')
	);

	foreach ($model->fields as $field => $data) {
		if (in_array($field, $model->admin['fileFields'])) {
			continue;
		}

		$classes = array($data['type']);

		if (isset($this->data[$model->alias][$field]) && $this->data[$model->alias][$field] !== '') {
			$classes[] = 'warning';
		} ?>

		<div class="control-group <?php echo implode(' ', $classes); ?>">
			<?php echo $this->Form->label($field, $data['title'], array('class' => 'control-label')); ?>

			<div class="controls">
				<?php
				// Belongs to is the only special case
				if (!empty($data['belongsTo'])) {
					echo $this->element('input/belongs_to', array(
						'field' => $field,
						'data' => $data
					));

				// Display a comparison dropdown for filters
				} else if (in_array($data['type'], array('integer', 'datetime'))) { ?>

					<div class="input-prepend">
						<?php
						echo $this->element('input_filter', array(
							'field' => $field,
							'data' => $data
						));

						echo $this->element('input/filter', array(
							'field' => $field,
							'data' => $data
						)); ?>
					</div>

				<?php } else {
					echo $this->element('input/filter', array(
						'field' => $field,
						'data' => $data
					));
				} ?>
			</div>
		</div>

	<?php } ?>

	<button type="submit" class="btn btn-info">
		<?php echo __('Filter'); ?>
	</button>

	<a href="<?php echo $this->Html->url($reset); ?>" class="btn">
		<?php echo __('Reset'); ?>
	</a>

	<script type="text/javascript">
		$(function() {
			Admin.filterComparisons();
		});
	</script>

	<?php echo $this->Form->end(); ?>
</div>