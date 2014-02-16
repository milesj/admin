<div class="action-buttons">
    <?php
    foreach (CakeLog::configured() as $stream) {
        $class = 'button';

        if (isset($type) && $stream == $type) {
            $class .= ' is-active';
        }

        echo $this->Html->link(Inflector::humanize($stream), array('action' => 'read', $stream), array('class' => $class));
    } ?>
</div>