<?php
$this->Breadcrumb->add(__('Analyze Models'), array('controller' => 'admin', 'action' => 'analyze')); ?>

<h2><?php echo __('Models'); ?></h2>

<table class="table table-striped table-bordered sortable">
	<thead>
		<tr>
			<th><span><?php echo __('Model'); ?></span></th>
			<th><span class="tip" title="<?php echo __('Primary Key'); ?>"><?php echo __('PK'); ?></span></th>
			<th><span><?php echo __('Display Field'); ?></span></th>
			<th><span><?php echo __('Database'); ?></span></th>
			<th><span><?php echo __('Table'); ?></span></th>
			<th><span><?php echo __('Schema'); ?></span></th>
			<th><span><?php echo __('Behaviors'); ?></span></th>
			<th><span><?php echo __('Belongs To'); ?></span></th>
			<th><span><?php echo __('Has One'); ?></span></th>
			<th><span><?php echo __('Has Many'); ?></span></th>
			<th><span class="tip" title="<?php echo __('Has and Belongs to Many'); ?>"><?php echo __('HABTM'); ?></span></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($plugins as $plugin) { ?>

			<tr class="info">
				<td colspan="11"><b><?php echo $plugin['title']; ?></b></td>
			</tr>

			<?php foreach ($plugin['models'] as $model) {
				$object = $this->Admin->introspect($model['id']) ?>

			<tr>
				<td>
					<?php echo $this->Html->link($model['title'], array(
						'controller' => 'crud',
						'action' => 'index',
						'model' => $model['url']
					)); ?>
				</td>
				<td><?php echo $object->primaryKey; ?></td>
				<td>
					<?php if ($object->displayField == $object->primaryKey) { ?>
						<span class="label label-warning"><?php echo ('N/A'); ?></span>
					<?php } else {
						echo $object->displayField;
					} ?>
				</td>
				<td><?php echo $object->useDbConfig; ?></td>
				<td><?php echo $object->tablePrefix . $object->useTable; ?></td>
				<td><?php echo implode(', ', $this->Admin->normalizeArray($object->schema(), false)); ?></td>
				<td><?php echo implode(', ', $this->Admin->normalizeArray($object->actsAs)); ?></td>
				<td><?php echo implode(', ', $this->Admin->normalizeArray($object->belongsTo)); ?></td>
				<td><?php echo implode(', ', $this->Admin->normalizeArray($object->hasOne)); ?></td>
				<td><?php echo implode(', ', $this->Admin->normalizeArray($object->hasMany)); ?></td>
				<td><?php echo implode(', ', $this->Admin->normalizeArray($object->hasAndBelongsToMany)); ?></td>
			</tr>

		<?php } } ?>
	</tbody>
</table>
