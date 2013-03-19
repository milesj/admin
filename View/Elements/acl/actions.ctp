<div class="action-buttons">
	<?php
	echo $this->Html->link('<span class="icon-pencil icon-white"></span> ' . __('Add Requester'),
		array('controller' => 'crud', 'action' => 'create', 'model' => 'admin.request_object'),
		array('class' => 'btn btn-primary btn-large', 'escape' => false));

	echo $this->Html->link('<span class="icon-pencil icon-white"></span> ' . __('Add Controller'),
		array('controller' => 'crud', 'action' => 'create', 'model' => 'admin.control_object'),
		array('class' => 'btn btn-primary btn-large', 'escape' => false)); ?>
</div>