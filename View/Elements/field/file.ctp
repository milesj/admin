<?php
$title = $value . ' <span style="font-size: 10px" class="icon-external-link"></span>';

// Make relative URLs absolute, this should fix most cases
if (!preg_match('/^([a-z]+)s?:\/\//', $value) && $value[0] !== '/') {
	$value = '/' . $value;
}

echo $this->Html->link($title, $value, array('escape' => false));