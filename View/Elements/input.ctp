
<div class="control-group <?php echo $data['type']; ?>">
	<?php echo $this->Form->label($field, $data['title'], array('class' => 'control-label')); ?>

	<div class="controls">
		<?php
		$element = 'default';

		if (!empty($data['belongsTo'])) {
			$element = 'belongs_to';

		} else if ($field === 'id') {
			$element = 'id';

		} else if (in_array($field, $model->admin['fileFields'])) {
			$element = 'file';
		}

		echo $this->element('input/' . $element, array(
			'field' => $field,
			'data' => $data
		)); ?>
	</div>
</div>