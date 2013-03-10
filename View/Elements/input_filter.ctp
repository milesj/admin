<?php
$value = isset($this->data[$model->alias][$field . '_filter']) ? $this->data[$model->alias][$field . '_filter'] : '=';

echo $this->Form->input($field . '_filter', array(
	'type' => 'hidden',
	'div' => false,
	'error' => false,
	'label' => false,
	'value' => $value
)); ?>

<div class="btn-group">
	<button data-toggle="dropdown" class="btn dropdown-toggle">
		<?php echo $value; ?>
	</button>

	<ul class="dropdown-menu">
		<li><a href="javascript:;" data-filter="="><?php echo __('Equals'); ?></a></li>
		<li><a href="javascript:;" data-filter="!="><?php echo __('Not Equals'); ?></a></li>
		<li><a href="javascript:;" data-filter=">"><?php echo __('Greater Than'); ?></a></li>
		<li><a href="javascript:;" data-filter=">="><?php echo __('Greater Than or Equal'); ?></a></li>
		<li><a href="javascript:;" data-filter="<"><?php echo __('Less Than'); ?></a></li>
		<li><a href="javascript:;" data-filter="<="><?php echo __('Less Than or Equal'); ?></a></li>
	</ul>
</div>