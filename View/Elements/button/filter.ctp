<?php
echo $this->Html->link('<span class="fa fa-filter icon-white"></span> ' . __d('admin', 'Filter'), 'javascript:;', array(
    'class' => 'button',
    'id' => 'filter-toggle',
    'escape' => false
));