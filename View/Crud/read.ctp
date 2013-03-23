<?php
$this->Admin->setBreadcrumbs($model, $result, $this->action);

echo $this->element('crud/actions'); ?>

<h2><?php echo $this->Admin->outputIconTitle($model, $this->Admin->getDisplayField($model, $result)); ?></h2>

<div class="row-fluid">
	<?php echo $this->element('crud/read_table'); ?>
</div>

<?php echo $this->element('crud/read_extra'); ?>