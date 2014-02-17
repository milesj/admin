<?php
#debug($model);
echo $this->Html->link('<span class="fa fa-download icon-white"></span> ' . __d('admin', 'Export'), 
	array('action' => 'export', 'model' => $model->urlSlug), 
	array('class' => 'button', 'id' => 'export', 'escape' => false)
);