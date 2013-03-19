<?php
echo $this->Html->link('<span class="icon-filter icon-white"></span> ' . __('Filter'), 'javascript:;', array(
	'class' => 'btn btn-large',
	'id' => 'filter-toggle',
	'escape' => false,
	'onclick' => 'Admin.filterToggle(this);'
));