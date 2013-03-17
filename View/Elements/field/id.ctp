<?php
$url = array('action' => 'read', $value);

if ($this->params['controller'] === 'crud') {
	$url['model'] = $model->urlSlug;
}

echo $this->Html->link($value, $url);