<div class="action-buttons">
    <?php
    $links = Configure::read('Admin.actions');

    foreach ($links as $action => $link) {
        $class = 'button';
        $url = array('controller' => 'admin', 'action' => $action);

        if ($this->action === $action) {
            $class .= ' is-active';
        }

        if (!empty($link['url'])) {
            $url = $link['url'];
        }

        echo $this->Html->link('<span class="' . $link['icon'] . '"></span> ' . $link['title'],
            $url,
            array('class' => $class, 'escape' => false));
    } ?>

    <span class="clear"></span>
</div>